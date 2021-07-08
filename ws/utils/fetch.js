const fetch = require("node-fetch");
const { URLSearchParams } = require("url");

module.exports = (url, client, isPost, body, method) => {
  if (method == null) method = "POST";
  if (isPost) {
    const params = new URLSearchParams();
    for (let [key, value] of Object.entries(body)) {
      params.append(key, value);
    }

    return fetch(url, {
      method: method,
      body: params,
      headers: { Authorization: "Bearer " + client.token },
    });
  }
  return fetch(url, {
    headers: { Authorization: "Bearer " + client.token },
  });
};
