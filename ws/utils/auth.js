const _ = require("lodash"); //lodash aka le compagnon de tous les jours
const fetch = require("node-fetch");

const getParameterByName = (name, url) => {
  name = name.replace(/[\[\]]/g, "\\$&");
  var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
    results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return "";
  return decodeURIComponent(results[2].replace(/\+/g, " "));
};

module.exports = (req) => {
  return new Promise((resolve, reject) => {
    var token = "";

    var t = getParameterByName("token", req.url);
    if (t != undefined && t != "") token = "Bearer " + t;

    if (token == undefined || token == "") {
      console.log("user unauthorized");
      return cb(true);
    }

    //on appel l'api pour tester sa validité
    fetch("http://localhost:8000/api/check", {
      headers: {
        Authorization: token,
      },
    })
      .then((res) => res.json())
      .then((json) => {
        if (json.user == undefined) {
          reject();
        } else {
          resolve(json.user);
        }
      })
      .catch((e) => {
        reject();
      });
  });
};
