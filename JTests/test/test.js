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

async function setGraph (data, token) {
    const res = await fetch('http://127.0.0.1:8080',{
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Token': token ? token : null
        },
        body: JSON.stringify({ query: `mutation ${data}`})
      });

    return res.json();
}

// describe('getting customers without authorization', function(){
//     it('should return no authorized when not token', async function(){
//
//          let res = await getGraph(`{
//             allCustomers{
//                 name
//             }
//          }`);
//
//      assert.equal(res.errors[0].message, 'no authorized');
// }) });

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

// describe('getting customers with wrong token', function(){
//     it('should return error no authorized when wrong token', async function(){
//
//          let res = await getGraph(`{
//             allCustomers{
//                 name
//             }
//          }`, tok+'brokenToken');
//
//         assert.isArray(res.errors, "has not errors");
//         assert.equal(res.errors[0].message, 'no authorized');
// }) });

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

let newCustomerUUID;
const customerName = 'Fide Castrol';

describe('add new customer', function(){
    it('should return added customer', async function(){

         let res = await setGraph(`{

              createCustomer(
                contacts: [
                    { typeId: 7, value: "Fidel-Castro"},
                    { typeId: 9, value: "fidelcastro"}
                    ],
                tags: [
                  { name: "fisher", color: "sky"},
                  { name: "tazik", color: "silver"}
                ]
                discount_card: 4264372,
                name: "${customerName}") {
                uuid
                name
                contacts{
                  value
                }
                tags{
                    name
                }
              }

         }`, tok);

        if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: "+res.errors[0].message);

        assert.exists(res.data, 'not data: '+res);

        newCustomerUUID = res.data.createCustomer.uuid;

        assert.deepEqual(res.data.createCustomer.contacts, [ { value: 'Fidel-Castro' }, { value: 'fidelcastro' } ], 'customer contains contacts');

        assert.deepEqual(res.data.createCustomer.tags, [ { name: 'fisher' }, { name: 'tazik' } ], 'customer contains tags');

        assert.equal(res.data.createCustomer.name, customerName, 'has not equal name');
}) });

const newCustomerName = "Fidel Castro";
const newDiscountCard = 5555555;

describe('update customer', function(){
    it('should return updated customer', async function(){

         let res = await setGraph(`{
         
          updateCustomer(
            uuid: "${newCustomerUUID}",
            name: "${newCustomerName}",
            discount_card: ${newDiscountCard},
            contacts: [
                    { typeId: 7, value: "Fidel-Castro"},
                    { typeId: 7, value: "NewContact"}
                    ])
            {
                uuid
                name
                discount_card
                contacts{
                  value
                }
            }
         }`, tok);

        if (Array.isArray(res.errors)) assert.exists(res.data, "has error: "+res.errors[0].message);

        assert.exists(res.data, 'not data: '+res);

        assert.deepEqual(res.data.updateCustomer.contacts, [ { value: 'Fidel-Castro' }, { value: 'NewContact' } ], 'customer contains contacts');

        assert.equal(res.data.updateCustomer.name, newCustomerName, 'has not equal name');
        assert.equal(res.data.updateCustomer.discount_card, newDiscountCard, 'has not equal discount card');
}) });

describe('delete customer', function(){
    it('should return deleted customer', async function(){

         let res = await setGraph(`{
            deleteCustomer(uuid: "${newCustomerUUID}"){
                name
            }

         }`, tok);

        if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: "+res.errors[0].message);

        assert.exists(res.data, 'not data: '+res);

        assert.equal(res.data.deleteCustomer.name, newCustomerName, 'has not equal name');
}) });

let newUserUUID;
const userLogin = "fex@i.ua";

describe('add new user', function(){
    it('should return added user', async function(){

         let res = await setGraph(`{
            createUser(login: "${userLogin}", password: "123"){
                uuid
                login
            }
         }`, tok);

        if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: "+res.errors[0].message);

        assert.exists(res.data, 'not data: '+res);

        newUserUUID = res.data.createUser.uuid;

        assert.equal(res.data.createUser.login, userLogin, 'has not equal login');
}) });

const newUserName = 'Felix'

describe('update user', function(){
    it('should return updated user', async function(){

         let res = await setGraph(`{
         
          updateUser(
            uuid: "${newUserUUID}",
            name: "${newUserName}",
            contacts: [
                    { typeId: 7, value: "catFelix"}
                    ])
            {
                uuid
                login
                name
                contacts{
                  value
                }
            }
         }`, tok);

        if (Array.isArray(res.errors)) assert.exists(res.data, "has error: "+res.errors[0].message);

        assert.exists(res.data, 'not data: '+res);

        assert.deepEqual(res.data.updateUser.contacts, [ { value: 'catFelix' } ], 'user contains contacts');

        assert.equal(res.data.updateUser.name, newUserName, 'has not equal name');
}) });

describe('delete new user', function(){
    it('should return deleted user', async function(){

         let res = await setGraph(`{
            deleteUser(uuid: "${newUserUUID}"){
                uuid
                login
            }
         }`, tok);

        if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: "+res.errors[0].message);

        assert.exists(res.data, 'not data: '+res);

        assert.equal(res.data.deleteUser.login, userLogin, 'has not equal login');
}) });