describe('Log in as admin', () => {
    it('should be able to log in as the default administrator', () => {
        cy.visit('/login')

        cy.wait(1500)

        cy.contains("Prihlásenie")
        cy.get('input[type="email"]').type('test@example.com')
        cy.get('input[type="password"]').type('password')
        cy.get('button[type="submit"]').click()

        cy.wait(1000)

        cy.contains("Vitajte")
        cy.location('pathname').should('eq', '/dashboard/admin')

        cy.wait(1000)

        cy.contains("Logout").click()
        cy.location('pathname').should('eq', '/')

        cy.wait(1000)
    })
})