const fetch = require("../utils/fetch");
const _ = require("lodash");
const WebSocket = require("ws");

module.exports = {
  type: 0xe6,
  call: (ws, request, client, data, clients) => {
    var service = data.data.service;
    var conversation = data.data.conversation;

    var user1 = conversation.sender;
    var user2 = conversation.receiver;

    console.log("creating alert message");

    var msg =
      "[ALERTE]\n\n Le service vient d'être accepté par " +
      service.recipient.username +
      " au prix de " +
      service.price +
      " €\n\n [ALERTE]";

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

          json.sender = json.sender;
          json.conversation = json.conversation.id;

          clients.forEach((az) => {
            if (az.readyState === WebSocket.OPEN) {
              if (az.client.id == user1.id || az.client.id == user2.id) {
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
        }
      })
      .catch((e) => {
        console.log(e);
      });
  },
};
