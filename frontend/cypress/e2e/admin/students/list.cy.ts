describe('Admin Student CRUD', () => {
    beforeEach(() => {
        cy.visit('/login')

        cy.wait(1000)

        cy.get('input[type="email"]').type('test@example.com')
        cy.get('input[type="password"]').type('password')
        cy.get('button[type="submit"]').click()

        cy.wait(1000)

        cy.contains("Študenti").click()
        cy.url().should('include', '/dashboard/admin/students')
    })

    it('should load the list of students in a proper format', () => {
        cy.get('table').within(() => {
            cy.contains('th', 'Meno').parent('tr').then(($headerRow) => {
                const nameColumnIndex = $headerRow.find('th').index(cy.$$('th:contains("Meno")')[0])

                cy.get('tbody tr').each(($row) => {
                    cy.wrap($row).find('td').eq(nameColumnIndex).invoke('text').then((name) => {
                        const nameParts = name.trim().split(' ')
                        expect(nameParts).to.have.length.at.least(2)
                        expect(nameParts[0]).to.not.be.empty
                        expect(nameParts[1]).to.not.be.empty
                    })
                })
            })

            cy.contains('th', 'E-mail').parent('tr').then(($headerRow) => {
                const nameColumnIndex = $headerRow.find('th').index(cy.$$('th:contains("E-mail")')[0])

                cy.get('tbody tr').each(($row) => {
                    cy.wrap($row).find('td').eq(nameColumnIndex).invoke('text').then((email) => {
                        expect(email).to.include("@student.ukf.sk")
                    })
                })
            })

            cy.contains('th', 'Telefón').parent('tr').then(($headerRow) => {
                const phoneColumnIndex = $headerRow.find('th').index(cy.$$('th:contains("Telefón")')[0])

                cy.get('tbody tr').each(($row) => {
                    cy.wrap($row).find('td').eq(phoneColumnIndex).invoke('text').then((phone) => {
                        expect(phone.trim()).to.not.be.empty
                    })
                })
            })

            cy.contains('th', 'Študijný program').parent('tr').then(($headerRow) => {
                const programColumnIndex = $headerRow.find('th').index(cy.$$('th:contains("Študijný program")')[0])

                cy.get('tbody tr').each(($row) => {
                    cy.wrap($row).find('td').eq(programColumnIndex).invoke('text').then((program) => {
                        expect(program.trim()).to.not.be.empty
                    })
                })
            })

            cy.contains('th', 'Osobný e-mail').parent('tr').then(($headerRow) => {
                const personalEmailColumnIndex = $headerRow.find('th').index(cy.$$('th:contains("Osobný e-mail")')[0])

                cy.get('tbody tr').each(($row) => {
                    cy.wrap($row).find('td').eq(personalEmailColumnIndex).invoke('text').then((personalEmail) => {
                        expect(personalEmail.trim()).to.not.be.empty
                        expect(personalEmail.trim()).to.include("@")
                    })
                })
            })

            cy.contains('th', 'Adresa').parent('tr').then(($headerRow) => {
                const addressColumnIndex = $headerRow.find('th').index(cy.$$('th:contains("Adresa")')[0])

                cy.get('tbody tr').each(($row) => {
                    cy.wrap($row).find('td').eq(addressColumnIndex).invoke('text').then((address) => {
                        expect(address.trim()).to.not.be.empty
                    })
                })
            })
        })
    })

    it('should be able to delete a student', () => {
        let initialRowCount = 0

        cy.get('table tbody tr').its('length').then((count) => {
            initialRowCount = count
        })

        cy.get('table tbody tr').first().within(() => {
            cy.contains('Vymazať').click()
        })

        cy.contains("Potvrdiť vymazanie").parent().should('be.visible')
        cy.contains("Potvrdiť vymazanie").parent().contains("Vymazať").click()
        cy.contains("Potvrdiť vymazanie").should('not.exist')

        cy.get('table tbody tr').its('length').then((count) => {
            expect(count).to.be.eq(initialRowCount - 1)
        })
    })
})