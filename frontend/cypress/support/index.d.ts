/// <reference types="cypress" />

declare namespace Cypress {
    interface Chainable {
        /**
         * Custom command to validate all cells in a table column
         * @param columnName - The name of the column header to validate
         * @param validator - A function that receives the cell text and performs assertions
         * @example cy.validateColumn('Email', (email) => { expect(email).to.include('@') })
         */
        validateColumn(columnName: string, validator: (value: string) => void): Chainable<void>
    }
}
