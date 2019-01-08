
const assert = require("chai").assert;

const getGraph = require('../utils').getGraph;
const setGraph = require('../utils').setGraph;

module.exports = function(tok) {
    return function () {

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

        describe('getting all customers', function(){
            it('should return array of customers', async function(){

                let res = await getGraph(`{
            allCustomers{
                name
            }
         }`, tok);

                assert.typeOf(res.data.allCustomers, 'array', 'returned customers is not array');
                // assert.typeOf(res.data.allCustomers, 'array', 'returned customers is not array');
            }) });
        let newCustomerUUID;
        const customerName = 'Fide Castrol';

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
}
}
