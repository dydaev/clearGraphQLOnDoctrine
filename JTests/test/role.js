
const assert = require("chai").assert;

const getGraph = require('../utils').getGraph;
const setGraph = require('../utils').setGraph;

module.exports = function(tok) {
    return function () {

        describe('add new guest', function() {

            let newGuestUUID;
            const userLogin = "guest@i.ua";

            it('should create guest', async function () {

                let res = await setGraph(`{
                    createUser(login: "${userLogin}", password: "222"){
                        uuid
                        login
                    }
                 }`, tok);

                if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: " + res.errors[0].message);

                assert.exists(res.data, 'not data: ' + res);

                newGuestUUID = res.data.createUser.uuid;

                assert.equal(res.data.createUser.login, userLogin, 'has not equal login');
            });

            let roleID;
            const roleName = "GUEST";

            describe('testing role', function () {

                // let roleID;

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

                it('should add guest role', async function () {

                    let res = await setGraph(`{
     
                          updateUserRoles(
                            uuid: "${newGuestUUID}",
                            roles: [
                                    { name: "${roleName}" }
                                    ])
                            {
                                login
                                roles{
                                    description
                                }
                            }
                         }`, tok);

                    if (Array.isArray(res.errors)) assert.exists(res.data, "has error: " + res.errors[0].message);

                    assert.exists(res.data, 'not data: ' + res);
                    assert.deepEqual(res.data.updateUserRoles.roles, [{"description":"role for all guest"}], 'user contains role');

                });

                it('deleting role, should return exception, role is used', async function () {

                    let res = await setGraph(`{
                            deleteRole(id: ${roleID}){
                                description
                            }
                         }`, tok);

                    assert.equal(res.errors[0].message, 'delete role is failed', 'has error');
                });

                it('should return guest by id without role', async function () {

                    let res = await getGraph(`{
     
                          userById(uuid: "${newGuestUUID}")
                            {
                                login
                                roles{
                                    description
                                }
                            }
                         }`, tok);

                    if (Array.isArray(res.errors)) assert.exists(res.data, "has error: " + res.errors[0].message);

                    assert.exists(res.data, 'not data: ' + res);

                    assert.equal(res.data.userById.login, userLogin, 'user contains login');

                    assert.deepEqual(res.data.userById.roles, [{"description":"role for all guest"}], 'user contains role');

                    // assert.equal(res.data.updateUser.name, newUserName, 'has not equal name');
                });

                describe('testing rule', function () {

                    let ruleReadFaceID;
                    let ruleReadWriteFaceID;
                    const essence = 'customer';
                    const rulePath = "*/contacts/facebook";
                    const pulePermission = 1;

                    it('should create new readOnly rule', async function () {

                        let res = await setGraph(`{
                            createRule(essence: "${essence}",rulePath: "${rulePath}", permission: ${pulePermission}, description: "reading customers facebooks"){
                                id
                                essence
                                rulePath
                                permission
                                description
                            }
                         }`, tok);

                        if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: " + res.errors[0].message);

                        assert.exists(res.data, 'not data: ' + res);

                        ruleReadFaceID = res.data.createRule.id;

                        assert.equal(res['data']['createRule']['description'], "reading customers facebooks", 'has not equal rule permission');
                        assert.equal(res.data.createRule.rulePath, rulePath, 'has not equal rule path');
                        assert.equal(res.data.createRule.permission, pulePermission, 'has not equal rule permission');

                        describe('using readOnly rule with guest role', function () {
                            it('should add rule to role', async function () {

                                const rulesList = [ruleReadFaceID];

                                let res = await setGraph(`{
                                    updateRoleRules(roleId: ${roleID}, rulesId: ${rulesList}){
                                        rules {
                                            description
                                        }
                                    }
                                 }`, tok);

                                if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: " + res.errors[0].message);

                                assert.exists(res.data, 'not data: ' + res);

                                assert.equal(res.data.updateRoleRules.rules[0].description, 'reading customers facebooks', 'has not equal rule description');
                            })
                        });
                    });


                    it('should create new ReadWrite rule', async function () {

                        let res = await setGraph(`{
                            createRule(essence: "${essence}",rulePath: "${rulePath}", permission: 3, description: "reading and writing customers facebooks"){
                                id
                                rulePath
                                permission
                                description
                            }
                         }`, tok);

                        if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: " + res.errors[0].message);

                        assert.exists(res.data, 'not data: ' + res);

                        ruleReadWriteFaceID = res.data.createRule.id;

                        assert.equal(res.data.createRule.description, "reading and writing customers facebooks", 'has not equal rule permission');
                        assert.equal(res.data.createRule.rulePath, rulePath, 'has not equal rule path');
                        assert.equal(res.data.createRule.permission, 3, 'has not equal rule permission');


                        describe('test readOnly rule', function () {
                            it('should return exception deleting rule, rule is used in role', async function () {

                                let res = await setGraph(`{
                                    deleteRule(id: ${ruleReadFaceID}){
                                        description
                                    }
                                 }`, tok);

                                assert.exists(res.data, 'not data: ' + res);

                                assert.equal(res.errors[0].message, 'delete rule is failed, what want wrong', 'has error');
                            });

                            it('should remove rule from guest role', async function () {

                                const emptyRulesList = [];

                                let res = await setGraph(`{
                                    updateRoleRules(roleId: ${roleID}, rulesId: [${emptyRulesList}]){
                                        rules {
                                            description
                                        }
                                    }
                                 }`, tok);

                                if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: " + res.errors[0].message);

                                assert.exists(res.data, 'not data: ' + res);

                                assert.lengthOf(res.data.updateRoleRules.rules, 0, 'role has empty rules');
                            });

                            it('should return deleted readOnly rule', async function () {

                                let res = await setGraph(`{
                                    deleteRule(id: ${ruleReadFaceID}){
                                        description
                                    }
                                 }`, tok);

                                if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: " + res.errors[0].message);

                                assert.exists(res.data, 'not data: ' + res);

                                assert.equal(res.data.deleteRule.description, 'reading customers facebooks', 'has not equal description');
                            })

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


                    })

                    // describe('test readWrite rule', function () {
                    it('should return deleted readWrite rule', async function () {

                        let res = await setGraph(`{
                                deleteRule(id: ${ruleReadWriteFaceID}){
                                    description
                                }
                             }`, tok);

                        if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: " + res.errors[0].message);

                        assert.exists(res.data, 'not data: ' + res);

                        assert.equal(res.data.deleteRule.description, 'reading and writing customers facebooks', 'has not equal description');
                    })
                    // });

                });

            });
            describe('finish testing guest', function () {
                it('should delete guest', async function () {

                    let res = await setGraph(`{
                    deleteUser(uuid: "${newGuestUUID}"){
                        uuid
                        login
                    }
                 }`, tok);

                    if (Array.isArray(res.errors)) assert.isNotArray(res.errors, "has error: " + res.errors[0].message);

                    assert.exists(res.data, 'not data: ' + res);

                    assert.equal(res.data.deleteUser.login, userLogin, 'has not equal login');
                });
            })
        })

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
