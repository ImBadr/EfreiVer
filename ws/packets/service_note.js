const fetch = require("../utils/fetch");
const _ = require("lodash");
const WebSocket = require("ws");
const moment = require("moment");

module.exports = {
  type: 0xe2,
  call: (ws, request, client, data, clients) => {
    var service = data.data.service;
    var conversation = data.data.conversation;
    var opinion = data.data.opinion;
    var sender = data.data.user;

    var user1 = conversation.sender;
    var user2 = conversation.receiver;

    var msg =
      "[ALERTE]\n\n Un avis a été déposé pour le service du " +
      moment.unix(opinion.createdAt / 1000).format("MM/DD/YYYY à hh:mm:ss") +
      "\n\nAuteur: " +
      sender.username +
      "\nNote: " +
      opinion.grade +
      "/5\nAvis:" +
      opinion.description +
      "\n\n [ALERTE]";

    fetch("http://localhost:8000/api/message", client, true, {
      text: msg.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
        return "&#" + i.charCodeAt(0) + ";";
      }),
      sender: 7,
      conversation: conversation.id,
    })
      .then((res) => res.json())
      .then((json) => {
        if (json.id) {
          var user1 = json.conversation.sender;
          var user2 = json.conversation.receiver;

          json.sender = json.sender.id;
          json.conversation = json.conversation.id;

          clients.forEach((az) => {
            if (az.readyState === WebSocket.OPEN) {
              if (az.client.id == user1 || az.client.id == user2) {
                console.log("sending alert to " + az.client.email);
                az.send(
                  JSON.stringify({
                    type: 0x10,
                    data: {
                      conversation: conversation.id,
                      json,
                    },
                  })
                );
                az.send(
                  JSON.stringify({
                    type: 0xe0,
                    data: "refresh conversations datas",
                  })
                );
              }
            }
          });
        } else {
          console.log("send 2");
        }
      })
      .catch((e) => {
        console.log(e);
      });
  },
};
