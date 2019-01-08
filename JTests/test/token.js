
const assert = require("chai").assert;

const getGraph = require('../utils').getGraph;
const setGraph = require('../utils').setGraph;

module.exports = function(tok) {
    return function () {
        describe('getting customers without authorization', function(){
            it('should return no authorized when not token', async function(){

                 let res = await getGraph(`{
                    allCustomers{
                        name
                    }
                 }`);

             assert.equal(res.errors[0].message, 'no authorized');
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
    }
}
