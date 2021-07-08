//0x01 <= ping
//0x02 => pong

//0x05 => ask conversations
//0x06 <= return conversations

//0x07 => create msg
//0x08 <= status msg

//0x09 => opening conv (seen)
//0x10 <= alert new msg
//0x11 <= msg seen

//0xE0 <= Update Conversations Datas
//0xE1 => service created
//0xE2 => note submitted
//0xE3 => litige created
//0xE4 => service terminated
//0xE5 => price edited
//0xE6 => negociation end

//0xFE => report conversations
//0xFF => report msg

const server = require("./server");
const bootstrap = require("./bin/bootstrap");

bootstrap().then((packets) => {
  server(packets).listen(9845, () =>
    console.log("Websocket server is listening on port 9845")
  );
});
