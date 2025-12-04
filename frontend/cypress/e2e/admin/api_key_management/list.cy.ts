describe('Admin API Key Management CRUD', () => {
    beforeEach(() => {
        cy.visit('/login')

        cy.wait(3000)

        cy.get('input[type="email"]').type('test@example.com')
        cy.get('input[type="password"]').type('password')
        cy.get('button[type="submit"]').click()

        cy.wait(1000)

        cy.contains("API Manažment").click()
        cy.url().should('include', '/dashboard/admin/external_api')
    })

    it('should load the list of API keys', () => {
        cy.get('table').within(() => {
            cy.validateColumn('Názov', (name) => {
                expect(name).to.not.be.empty
            })

            cy.validateColumn('Vytvorené', (created_at) => {
                expect(created_at).to.not.be.empty
            })

            cy.validateColumn('Naposledy použité', (last_used_at) => {
                expect(last_used_at).to.not.be.empty
            })

            cy.validateColumn('Vlastník', (owner) => {
                const nameParts = owner.trim().split(' ')
                expect(nameParts).to.have.length.at.least(2)
                expect(nameParts[0]).to.not.be.empty
                expect(nameParts[1]).to.not.be.empty
            })

            cy.contains('th', 'Operácie').parent('tr')
        })
    })

    it('should be able to create an API key', () => {
        // vytvorenie nového kľúča
        cy.contains("Pridať").click()
        cy.get('#newKeyName').type('cypress-e2e-test-key')
        cy.contains("Vytvoriť").click()

        cy.wait(3000)
        cy.contains("Zavrieť").click()

        // mali by sme mať práve 1 kľúč
        cy.get('table tbody tr').its('length').then((count) => {
            expect(count).to.be.greaterThan(0)
        })

        // Kontrola názvu
        cy.contains('cypress-e2e-test-key').should('be.visible')
    })

    it('should be able to delete an API key', () => {
        let initialRowCount = 0

        cy.get('table tbody tr').its('length').then((count) => {
            initialRowCount = count
        })

        // získanie prvého riadka z tabuľky
        cy.get('table tbody tr').then($rows => {
            const selectedRow = [...$rows].find(row =>
                Cypress.$(row).find('td').first().text().trim() === 'cypress-e2e-test-key'
            );
            cy.wrap(selectedRow).as('selectedRow');
        })

        // kliknutie na "Vymazať"
        cy.get('@selectedRow').within(() => {
            cy.contains('Vymazať').click()
        })

        // potvrdenie
        cy.contains(' Áno ').click()
        cy.wait(3000)

        cy.contains('cypress-e2e-test-key').should('not.exist')
    })
})