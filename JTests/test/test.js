const assert = require("chai").assert;

const getGraph = require('../utils').getGraph;
const setGraph = require('../utils').setGraph;

const customerTests = require('./customer');
const userTests = require('./user');
const tokenTests = require('./token');
const roleTests = require('./role');

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

// describe('testing authorization', tokenTests(tok));

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

describe('testing customer', customerTests(tok));

describe('testing user', userTests(tok));

describe('testing guest', roleTests(tok));
