const assert = require("chai").assert;
const fetch = require('node-fetch');

async function getGraph (data, token) {
    const res = await fetch('http://127.0.0.1:8080',{
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Token': token ? token : null
        },
        body: JSON.stringify({ query: data })
      });

    return res.json();
}

describe('getting customers without authorization', function(){
    it('should return no authorized when not token', async function(){

         let res = await getGraph(`{
            allCustomers{
                name
            }
         }`);

     assert.equal(res.errors[0].message, 'no authorized');
}) });

let tok = '';

describe('getting new token', function(){
    it('should return new token', async function(){

         let res = await getGraph(`
             {
              authorization(login: "roma@i.ua", password: "123qwe") {
                token
                life_time
              }
            }
        `);

        tok = res.data.authorization.token;
        assert.typeOf(tok, 'string', 'token is not string');
        assert.lengthOf(tok, 1024, 'token dos not has needed length');
}) });

describe('getting all customers (authorized)', function(){
    it('should return array of customers', async function(){

         let res = await getGraph(`{
            allCustomers{
                name
            }
         }`, tok);

         assert.typeOf(res.data.allCustomers, 'array', 'returned customers is not array');
         // assert.typeOf(res.data.allCustomers, 'array', 'returned customers is not array');
}) });

describe('getting customers with wrong token', function(){
    it('should return error no authorized when wrong token', async function(){

         let res = await getGraph(`{
            allCustomers{
                name
            }
         }`, tok+'brokenToken');

        assert.isArray(res.errors, "has not errors");
        assert.equal(res.errors[0].message, 'no authorized');
}) });