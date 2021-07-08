const WebSocket = require("ws");
const http = require("http");
const wss = new WebSocket.Server({ noServer: true });
const auth = require("./utils/auth");

module.exports = (packets) => {
  const server = http.createServer((req, res) => {
    res.setHeader("Content-Type", "application/json");
    res.write(
      JSON.stringify({
        error: "403",
        data: "forbidden",
      })
    );
    res.end();
  });

  function heartbeat(ws) {
    ws.isAlive = true;
  }

  wss.on("connection", (ws, req, client) => {
    ws.isAlive = true;
    ws.client = client;

    var ip =
      (req.headers["x-forwarded-for"] || "").split(",").pop() ||
      req.connection.remoteAddress ||
      req.socket.remoteAddress ||
      req.connection.socket.remoteAddress;

    ws.ip = ip.trim();

    console.log("new connection from " + ip);

    ws.on("message", function message(msg) {
      var json = JSON.parse(msg);
      if (json.type != 0x02)
        console.log("[" + ws.ip + "] : " + JSON.stringify(json));

      if (json.type == 0x02) heartbeat(ws);
      packets.forEach((packet) => {
        if (packet.type == json.type) {
          packet.call(ws, req, client, json, wss.clients);
        }
      });
    });
  });

  /**
   * Au niveau des sockets, l'évènement UPGRADE intervient lorsqu'un client s'apprete à se connecter au serveur,
   *  c'est cette évènement qui va dire si oui ou non le client à le droit de ce connecter
   */
  server.on("upgrade", (request, socket, head) => {
    /**
     * ici on appel la fonction authenticate qui va permettre de savoir si le client qui se connecte au serveur
     *  est bien un client connecté sur l'application
     */
    auth(request)
      .then((client) => {
        //si tout est bon, on indique au client qu'il peut se connecter
        wss.handleUpgrade(request, socket, head, (ws) => {
          wss.emit("connection", ws, request, client);
        });
      })
      .catch(() => {
        socket.destroy();
      });
  });

  /**
   * ces deux intervals permettent de déconnecter automatiquement les clients qui ne répondent pas au ping durant 30s.
   * comme ça on sait qu'on travail exclusivement avec des clients connectés
   * ça evite d'avoir des erreurs à la con parce que la socket est close mais le client est toujours connecté
   *
   * le premier interval c'est pour envoyer un ping toute les 5s
   * l'autre pour vérif si au bout de 30s bah il a tjrs pas répondu au ping
   */
  setInterval(function ping() {
    wss.clients.forEach(function each(ws) {
      if (ws.readyState === WebSocket.OPEN) {
        ws.isAlive = false;
        ws.send(
          JSON.stringify({
            type: 0x01,
            data: "ping",
          })
        );
      }
    });
  }, 5000);

  setInterval(function ping() {
    wss.clients.forEach(function each(ws) {
      if (ws.isAlive === false) return ws.terminate();
    });
  }, 30000);

  return server;
};
