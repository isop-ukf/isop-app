describe('Admin Dashboard', () => {
    beforeEach(() => {
        cy.visit('/login')

        cy.wait(1000)

        cy.get('input[type="email"]').type('test@example.com')
        cy.get('input[type="password"]').type('password')
        cy.get('button[type="submit"]').click()

        cy.wait(1000)
    })

    it('should load the list of companies', () => {
        cy.contains("Firmy").click()
        cy.url().should('include', '/dashboard/admin/companies')
        cy.get("table").find("tr").should("have.length.greaterThan", 1)
    })

    it('should load the list of students', () => {
        cy.contains("Študenti").click()
        cy.url().should('include', '/dashboard/admin/students')
        cy.get("table").find("tr").should("have.length.greaterThan", 1)
    })

    it('should load the list of internships', () => {
        cy.contains("Praxe").click()
        cy.url().should('include', '/dashboard/admin/internships')
        cy.get("table").find("tr").should("have.length.greaterThan", 1)
    })

    it('should load the my account details', () => {
        cy.contains("Môj profil").click()
        cy.url().should('include', '/account')

        cy.contains("Môj profil")
        cy.contains("Osobné údaje")
        cy.contains("Meno")
        cy.contains("Priezvisko")

        // check if the names match the default test user
        cy.get('#page-container-card > div:nth-child(5) > div:nth-child(1) > div > div.v-list-item-subtitle').should('have.text', 'Test')
        cy.get("#page-container-card > div:nth-child(5) > div:nth-child(2) > div > div.v-list-item-subtitle").should('have.text', 'User')

        // check if the "change my password" button is there
        cy.get("#page-container-card > div:nth-child(6) > div > div > a > span.v-btn__content").invoke('text').then(text => text.trim()).should('equal', 'Zmeniť heslo')
    })
})