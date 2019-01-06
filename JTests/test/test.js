const chai = require("chai");
const fetch = require('node-fetch');

const assert = chai.assert;

async function getGraph (data, token) {
    const res = await fetch('http://localhost:8080',{
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'token': token ? token : null
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

let tok;

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
    it('should return all customers', async function(){

         let res = await getGraph(`{
            allCustomers{
                name
            }
         }`, tok);

     assert.equal(res.data, );
}) });