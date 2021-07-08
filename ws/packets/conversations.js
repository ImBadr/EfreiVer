const fetch = require("../utils/fetch");
const _ = require("lodash");

module.exports = {
  type: 0x05,
  call: (ws, request, client, data) => {
    console.log("conversations");
    fetch("http://localhost:8000/api/conversations", client)
      .then((res) => res.json())
      .then((json) => {
        ws.send(
          JSON.stringify({
            type: 0x06,
            data: _.map(json, (conversation) => {
              var t =
                _.indexOf(
                  conversation.messages,
                  _.find(
                    conversation.messages,
                    (item) => item.sender != client.id && item.seen == false
                  )
                ) > 0;
              return {
                with:
                  conversation.sender.id == client.id
                    ? conversation.receiver
                    : conversation.sender,
                id: conversation.id,
                last:
                  conversation.messages.length == 0
                    ? conversation.created
                    : conversation.messages[conversation.messages.length - 1]
                        .created,
                messages: conversation.messages,
                hasNewMessage: t,
              };
            }),
          })
        );
      })
      .catch((e) => {
        console.log(e);
      });
  },
};
