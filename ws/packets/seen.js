const fetch = require("../utils/fetch");
const _ = require("lodash");
const WebSocket = require("ws");

module.exports = {
  type: 0x09,
  call: (ws, request, client, data, clients) => {
    var convId = data.conv;

    fetch("http://localhost:8000/api/conversation/" + convId, client)
      .then((res) => res.json())
      .then((json) => {
        json.messages.forEach((message) => {
          fetch(
            "http://localhost:8000/api/message/" + message.id + "/seen",
            client
          )
            .then((res) => res.json())
            .then((json) => {
              if (json.id != undefined) {
                var user1 = json.conversation.sender.id;
                var user2 = json.conversation.receiver.id;
                var user = -1;

                if (user1 == client.id) user = user2;
                else user = user1;

                clients.forEach((az) => {
                  if (az.readyState === WebSocket.OPEN) {
                    if (az.client.id == user) {
                      setTimeout(() => {
                        az.send(
                          JSON.stringify({
                            type: 0x11,
                            data: { conversation: convId, message: message.id },
                          })
                        );
                      }, 1000);
                    }
                  }
                });
              }
            })
            .catch((e) => {
              console.log(e);
            });
        });
      })
      .catch((e) => {
        console.log(e);
      });
  },
};
