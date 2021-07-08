import _ from "lodash";
import moment from "moment";

components["conversations"] = {
  delimiters: ["${", "}"],
  template: "#conversations",
  mounted() {
    try {
      this.$options.sockets.onopen = () => {
        this.$bus.$emit("socket-on-open");

        this.$options.sockets.onmessage = (data) => {
          var json = JSON.parse(data.data);

          if (json.type == 0x01) {
            this.$socket.send(JSON.stringify({ type: 0x02, data: "pong" }));
          } else {
            this.$bus.$emit("socket-on-message", json);
          }
        };
      };

      this.$options.sockets.onclose = () => {
        this.$bus.$emit("socket-on-close");
      };

      this.$options.sockets.onerror = (e) => {
        this.$bus.$emit("socket-on-error", e);
      };

      this.$bus.$on("socket-send", (data) => {
        this.$socket.send(JSON.stringify(data));
      });
    } catch (e) {
      console.log(e);
    }

    this.$bus.$on("socket-on-open", () => {
      this.$socket.send(JSON.stringify({ type: 0x05, data: "" }));
    });

    this.$bus.$on("socket-on-message", (data) => {
      console.log(data);
      if (data.type == 0x06) {
        this.conversations = [];
        data.data.forEach((element) => {
          var tmp = element;
          tmp.fakes = [];
          this.conversations.push(tmp);
        });
      }

      if (data.type == 0x10) {
        var index = _.indexOf(
          this.conversations,
          _.find(
            this.conversations,
            (item) => item.id == data.data.json.conversation
          )
        );
        if (!(index == undefined || index == -1)) {
          this.conversations[index].messages.push(data.data.json);

          var t =
            _.indexOf(
              this.conversations[index].messages,
              _.find(
                this.conversations[index].messages,
                (item) => item.sender != USER_ID && item.seen == false
              )
            ) > 0;

          if (this.activeConv.id == this.conversations[index].id) {
            this.$socket.send(
              JSON.stringify({
                type: 0x09,
                data: "seen",
                conv: this.activeConv.id,
              })
            );
          } else {
            this.conversations[index].hasNewMessage = t;
          }
        }
      }

      if (data.type == 0x11) {
        var index = _.indexOf(
          this.conversations,
          _.find(
            this.conversations,
            (item) => item.id == data.data.conversation
          )
        );
        if (!(index == undefined || index == -1)) {
          var index2 = _.indexOf(
            this.conversations[index].messages,
            _.find(
              this.conversations[index].messages,
              (item) => item.id == data.data.message
            )
          );
          if (!(index2 == undefined || index2 == -1)) {
            this.conversations[index].messages[index2].seen = true;
          }
        }
      }
    });
  },
  data() {
    return {
      conversations: _.map(CONVERSATIONS, (conversation) => {
        var t =
          _.indexOf(
            conversation.messages,
            _.find(
              conversation.messages,
              (item) => item.sender != USER_ID && item.seen == false
            )
          ) > 0;

        return {
          with:
            conversation.sender.id == USER_ID
              ? conversation.receiver
              : conversation.sender,
          id: conversation.id,
          last:
            conversation.messages.length == 0
              ? conversation.created
              : conversation.messages[conversation.messages.length - 1].created,
          messages: conversation.messages,
          fakes: [],
          hasNewMessage: t,
        };
      }),
      openDm: false,
      activeConv: {},
      errorFormNew: "",
      userNoteForm: {
        name: "",
        firstName: "",
        id: 0,
      },
      serviceNoteForm: {
        category: {
          name: "",
          id: 0,
        },
        price: "",
        createdAt: 0,
        id: 0,
      },
      message: "",
      dropdownOpen: false,
      pastServices: [],
      actualServices: [],
    };
  },
  methods: {
    format(date) {
      return moment.unix(date / 1000).format("MM/DD/YYYY");
    },
    open(conv) {
      if (conv.fakes == undefined) conv.fakes = [];
      this.activeConv = conv;
      this.openDm = true;
      setTimeout(() => {
        try {
          this.$socket.send(
            JSON.stringify({
              type: 0x09,
              data: "seen",
              conv: this.activeConv.id,
            })
          );
        } catch (e) {}
      }, 1000);
      this.activeConv.hasNewMessage = false;
    },
    addFake(fake) {
      this.activeConv.fakes.push(fake);
    },
    removeFake(id) {
      var index = _.indexOf(
        this.activeConv.fakes,
        _.find(this.activeConv.fakes, (item) => item.id == id)
      );
      if (!(index == undefined || index == -1)) {
        _.remove(this.activeConv.fakes, {
          id,
        });
      }
    },
    addMsg(msg) {
      var index = _.indexOf(
        this.activeConv.messages,
        _.find(this.activeConv.messages, (item) => item.id == msg.id)
      );
      if (index == undefined || index == -1) {
        this.activeConv.messages.push(msg);
      }
    },
    setError(id) {
      var index = _.indexOf(
        this.activeConv.fakes,
        _.find(this.activeConv.fakes, (item) => item.id == id)
      );
      if (!(index == undefined || index == -1))
        this.activeConv.fakes[index].error = true;
    },
    submitService(e) {
      var formData = new FormData();

      var client = 0;
      var recipient = 0;

      if (document.getElementById("isrecipient") == undefined) {
        client = USER_ID;
        recipient = this.activeConv.with.id;
      } else {
        recipient = USER_ID;
        client = this.activeConv.with.id;
      }

      var price = document.getElementById("price").value;
      var category = document.getElementById("category").value;

      formData.set("price", price);
      formData.set("client", client);
      formData.set("recipient", recipient);
      formData.set("category", category);

      this.$axios({
        method: "POST",
        url: "/service",
        data: formData,
      })
        .then((response) => {
          this.$modal.hide("new-service");
          this.$refs.datasConv.getDatas();
          this.$socket.send(
            JSON.stringify({
              type: 0xe1,
              data: { service: response.data, conversation: this.activeConv },
            })
          );
        })
        .catch((response) => {
          this.$notify({
            group: "alert",
            title: "Inscription",
            text: "Impossible de s'inscrire actuellement",
            type: "error",
          });
        });
    },
    beforeOpenNoteForm(event) {
      this.userNoteForm = event.params.user;
      this.serviceNoteForm = event.params.service;
    },
    beforeOpenEditForm(event) {
      this.serviceNoteForm = event.params.service;
    },
    showName(id) {
      if (this.activeConv.with.id == id) {
        return this.activeConv.with.name + " " + this.activeConv.with.firstName;
      } else {
        return "moi";
      }
    },
    isMe(id) {
      return id == USER_ID;
    },
    titleMsg(seen) {
      if (seen) return "message lu par le correspondant";
      else return "message envoyé";
    },
    text(msg) {
      return msg.split("\n").join("<br>");
    },
    toggleDropdown(id) {
      if (this.dropdownOpen == id) this.dropdownOpen = null;
      else this.dropdownOpen = id;
    },
    close(e) {
      if (!this.$el.contains(e.target)) {
        this.dropdownOpen = false;
      }
    },
    report() {
      this.$socket.send(JSON.stringify({ type: 0xff, data: this.message }));
      this.$notify({
        group: "alert",
        title: "Signalement",
        text:
          "Merci de votre signalement. Le message sera analysé par notre équipe sous peu.",
        type: "success",
      });
      this.dropdownOpen = false;
    },
    addFakeMessage(msg) {
      this.addFake({
        text: msg.msg,
        id: msg.id,
        conv: this.activeConv.id,
        error: false,
      });

      this.$bus.$on("socket-on-message", (data) => {
        if (data.type == 0x08) {
          var index = _.indexOf(
            this.activeConv.fakes,
            _.find(this.activeConv.fakes, (item) => item.id == data.data.fakeId)
          );
          if (!(index == undefined || index == -1)) {
            if (data.data.sent) {
              this.addMsg(data.data.json);
              this.removeFake(data.data.fakeId);
            } else {
              this.setError(data.data.fakeId);
            }
          }
        }
      });
    },
    display(message) {
      return this.activeConv.id == message.conv;
    },
    sendMsg() {
      var msg = this.message;
      var id = Math.floor(Math.random() * (new Date().getTime() / 1000) + 1);
      this.addFakeMessage({ id, msg });
      this.message = "";

      this.$socket.send(
        JSON.stringify({
          type: 0x07,
          data: { id, msg, conversation: this.activeConv.id },
        })
      );
    },

    linkOpinionToService(grade, service, opinion) {
      var formData = new FormData();

      if (grade == 0) {
        formData.set("opinion_client", opinion.id);
      } else {
        formData.set("opinion_recipient", opinion.id);
      }

      this.$axios({
        method: "PATCH",
        url: "/service/" + service,
        data: formData,
      })
        .then((response) => {
          if (response.data.id) {
            this.$modal.hide("note-service");
            this.$refs.datasConv.getDatas();
            this.$socket.send(
              JSON.stringify({
                type: 0xe2,
                data: {
                  user: this.$auth.user,
                  opinion,
                  service: this.serviceNoteForm,
                  conversation: this.activeConv,
                },
              })
            );
            this.$notify({
              group: "alert",
              title: "Notation",
              text: "Merci de nous avoir donné votre avis !",
              type: "success",
            });
          } else {
            this.$notify({
              group: "alert",
              title: "Notation",
              text: "Impossible de noter ce service actuellement",
              type: "error",
            });
          }
        })
        .catch((response) => {
          this.$notify({
            group: "alert",
            title: "Notation",
            text: "Impossible de noter ce service actuellement",
            type: "error",
          });
        });
    },
    format(date) {
      return moment(date).format("MM/DD/YYYY à hh:mm:ss");
    },
    avatar(id) {
      return "https://via.placeholder.com/50";
    },
    domDecoder(str) {
      let parser = new DOMParser();
      let dom = parser.parseFromString(
        "<!doctype html><body>" + str,
        "text/html"
      );
      if (dom.body.textContent.length > 75)
        return dom.body.textContent.substring(0, 75) + "...";
      else return dom.body.textContent;
    },
  },
};
