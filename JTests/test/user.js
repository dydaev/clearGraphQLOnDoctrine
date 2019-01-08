
const assert = require("chai").assert;

const getGraph = require('../utils').getGraph;
const setGraph = require('../utils').setGraph;

module.exports = function(tok) {
    return function () {

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

        describe('update user contacts', function(){
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
    }
}
