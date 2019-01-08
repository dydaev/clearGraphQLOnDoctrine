
const assert = require("chai").assert;

const getGraph = require('../utils').getGraph;
const setGraph = require('../utils').setGraph;

module.exports = function(tok) {
    return function () {

        describe('add new guest', function(){

            let newGuestUUID;
            const userLogin = "guest@i.ua";

            it('should create guest', async function(){

                let res = await setGraph(`{
                    createUser(login: "${userLogin}", password: "222"){
                        uuid
                        login
                    }
                 }`, tok);

                if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: "+res.errors[0].message);

                assert.exists(res.data, 'not data: '+res);

                newGuestUUID = res.data.createUser.uuid;

                assert.equal(res.data.createUser.login, userLogin, 'has not equal login');
            });

            const roleName = "GUEST";
            describe('testing role', function() {

                let roleID;

                it('should create new role', async function () {

                    let res = await setGraph(`{
                        createRole(name: "${roleName}", description: "role for all guest"){
                            id
                            name
                            description
                        }
                     }`, tok);

                    if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: " + res.errors[0].message);

                    assert.exists(res.data, 'not data: ' + res);

                    roleID = res.data.createRole.id;

                    assert.equal(res.data.createRole.name, roleName, 'has not equal role name');
                    assert.equal(res.data.createRole.description, "role for all guest", 'has not equal role description');

                });

                describe('testing rule', function(){

                    let ruleReadFaceID;
                    const rulePath = "customer/contacts/facebook";
                    const pulePermission = 1;

                    it('should create new readOnly rule', async function(){

                        let res = await setGraph(`{
                            createRule(rulePath: "${rulePath}", permission: ${pulePermission}, description: "reading customers facebooks"){
                                id
                                rulePath
                                permission
                                description
                            }
                         }`, tok);

                        if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: "+res.errors[0].message);

                        assert.exists(res.data, 'not data: '+res);

                        ruleReadFaceID = res.data.createRule.id;

                        assert.equal(res.data.createRule.permission, "reading customers facebooks", 'has not equal rule permission');
                        assert.equal(res.data.createRule.rulePath, rulePath, 'has not equal rule path');
                        assert.equal(res.data.createRule.permission, pulePermission, 'has not equal rule permission');
                    });

                    it('should create new ReadWrite rule', async function(){

                        let res = await setGraph(`{
                            createRule(rulePath: "${rulePath}", permission: 3, description: "reading and writing customers facebooks"){
                                id
                                rulePath
                                permission
                                description
                            }
                         }`, tok);

                        if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: "+res.errors[0].message);

                        assert.exists(res.data, 'not data: '+res);

                        const ruleReadWriteFaceID = res.data.createRule.id;

                        describe('delete readWrite rule', function(){
                            it('should return deleted rule', function*(){

                                let res = yield setGraph(`{
                                    deleteRule(id: "${ruleReadWriteFaceID}"){
                                        description
                                    }
                                 }`, tok);

                                if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: "+res.errors[0].message);

                                assert.exists(res.data, 'not data: '+res);

                                assert.equal(res.data.deleteRule.description, 'reading and writing customers facebooks', 'has not equal description');
                            }) });

                        assert.equal(res.data.createRule.permission, "reading and writing customers facebooks", 'has not equal rule permission');
                        assert.equal(res.data.createRule.rulePath, rulePath, 'has not equal rule path');
                        assert.equal(res.data.createRule.permission, pulePermission, 'has not equal rule permission');
                    })

                });

                it('should return deleted role', async function () {

                    let res = await setGraph(`{
                            deleteRole(id: ${roleID}){
                                description
                            }
                         }`, tok);

                    if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: " + res.errors[0].message);

                    assert.exists(res.data, 'not data: ' + res);

                    assert.equal(res.data.deleteRole.description, 'role for all guest', 'has not equal description');
                })

            });
            if (newGuestUUID) {
                describe('update guest', function () {
                    it('should return updated guest', async function () {

                        let res = await setGraph(`{
         
                              updateUser(
                                uuid: "${newGuestUUID}",
                                roles: [
                                        { "${roleName}" }
                                        ])
                                {
                                    login
                                    name
                                    roles{
                                        description
                                    }
                                }
                             }`, tok);

                        if (Array.isArray(res.errors)) assert.exists(res.data, "has error: " + res.errors[0].message);

                        assert.exists(res.data, 'not data: ' + res);

                        assert.deepEqual(res.data.updateUser.roles, [{description: 'role for all guest'}], 'user contains role');

                        // assert.equal(res.data.updateUser.name, newUserName, 'has not equal name');
                    })
                });
            }

            it('should delete guest', async function(){

                let res = await setGraph(`{
                    deleteUser(uuid: "${newGuestUUID}"){
                        uuid
                        login
                    }
                 }`, tok);

                if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: "+res.errors[0].message);

                assert.exists(res.data, 'not data: '+res);

                assert.equal(res.data.deleteUser.login, userLogin, 'has not equal login');
            })

        })





                // describe('add new ReadWrite rule', function(){
                //     });



                // describe('delete guest role', function() {
                //     it('should return deleted role ID:' + roleID, function* () {
                //
                //         let res = yield setGraph(`{
                //             deleteRole(id: "${roleID}"){
                //                 description
                //             }
                //          }`, tok);
                //
                //         if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: " + res.errors[0].message);
                //
                //         assert.exists(res.data, 'not data: ' + res);
                //
                //         assert.equal(res.data.deleteRole.description, 'role for all guest', 'has not equal description');
                //     })
                // });






    }
}
