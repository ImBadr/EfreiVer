const fetch = require("../utils/fetch");
const _ = require("lodash");
const WebSocket = require("ws");

module.exports = {
  type: 0x07,
  call: (ws, request, client, data, clients) => {
    fetch("http://localhost:8000/api/message", client, true, {
      text: data.data.msg.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
        return "&#" + i.charCodeAt(0) + ";";
      }),
      sender: client.id,
      conversation: data.data.conversation,
    })
      .then((res) => res.json())
      .then((json) => {
        if (json.id) {
          var user1 = json.conversation.sender;
          var user2 = json.conversation.receiver;
          var user = -1;

          json.sender = json.sender;
          json.conversation = json.conversation.id;
          ws.send(
            JSON.stringify({
              type: 0x08,
              data: {
                sent: true,
                conversation: data.data.conversation_id,
                json,
                fakeId: data.data.id,
              },
            })
          );

          if (user1.id == client.id) user = user2;
          else user = user1;
          clients.forEach((az) => {
            if (az.readyState === WebSocket.OPEN) {
              if (az.client.id == user.id) {
                az.send(
                  JSON.stringify({
                    type: 0x10,
                    data: {
                      conversation: data.data.conversation_id,
                      json,
                    },
                  })
                );
              }
            }
          });
        } else {
          ws.send(
            JSON.stringify({
              type: 0x08,
              data: {
                sent: false,
                conversation: data.data.conversation_id,
                fakeId: data.data.id,
              },
            })
          );
        }
      })
      .catch((e) => {
        console.log(e);
        ws.send(
          JSON.stringify({
            type: 0x08,
            data: {
              sent: false,
              conversation: data.data.conversation_id,
              fakeId: data.data.id,
            },
          })
        );
      });
  },
};
