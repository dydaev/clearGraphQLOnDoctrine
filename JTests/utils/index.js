const fetch = require('node-fetch');

const config = {
    host: 'http://127.0.0.1:8080',
    method: 'POST',
    headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'}
};
 module.exports = {

     getGraph: async function (data, token) {
         const res = await fetch(config.host, {
             method: config.method,
             headers: {
                 ...config.headers,
                 'Token': token ? token : null
             },
             body: JSON.stringify({query: data})
         });

         return res.json();
     },

     setGraph: async function (data, token) {
         const res = await fetch(config.host, {
             method: config.method,
             headers: {
                 ...config.headers,
                 'Token': token ? token : null
             },
             body: JSON.stringify({query: `mutation ${data}`})
         });

         return res.json();
     }
 }