describe('Admin Student CRUD', () => {
    beforeEach(() => {
        cy.visit('/login')

        cy.wait(3000)

        cy.get('input[type="email"]').type('test@example.com')
        cy.get('input[type="password"]').type('password')
        cy.get('button[type="submit"]').click()

        cy.wait(1000)

        cy.contains("Študenti").click()
        cy.url().should('include', '/dashboard/admin/students')
    })

    it('should load the list of students in a proper format', () => {
        cy.get('table').within(() => {
            cy.validateColumn('Meno', (name) => {
                const nameParts = name.trim().split(' ')
                expect(nameParts).to.have.length.at.least(2)
                expect(nameParts[0]).to.not.be.empty
                expect(nameParts[1]).to.not.be.empty
            })

            cy.validateColumn('E-mail', (email) => {
                expect(email).to.include("@student.ukf.sk")
            })

            cy.validateColumn('Telefón', (phone) => {
                expect(phone.trim()).to.not.be.empty
            })

            cy.validateColumn('Študijný program', (program) => {
                expect(program.trim()).to.not.be.empty
            })

            cy.validateColumn('Osobný e-mail', (personalEmail) => {
                expect(personalEmail.trim()).to.not.be.empty
                expect(personalEmail.trim()).to.include("@")
            })

            cy.validateColumn('Adresa', (address) => {
                expect(address.trim()).to.not.be.empty
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

        cy.wait(1000)

        cy.get('table tbody tr').its('length').then((count) => {
            expect(count).to.be.eq(initialRowCount - 1)
        })
    })

    it('should be able to edit a student', () => {
        // Náhodné sety
        const firstNames = ['Ján', 'Peter', 'Martin', 'Tomáš', 'Michal', 'Anna', 'Mária', 'Eva', 'Katarína', 'Lucia', 'Adam', 'Cypress']
        const lastNames = ['Novák', 'Kováč', 'Horváth', 'Varga', 'Molnár', 'Tóth', 'Nagy', 'Lukáč', 'Szabó', 'Kiss', 'Jablko', 'Tester']

        // Výber náhodného študenta
        cy.get('table tbody tr').then($rows => {
            const randomIndex = Math.floor(Math.random() * $rows.length)
            const randomRow = $rows.eq(randomIndex)

            cy.wrap(randomIndex).as('selectedIndex')
            cy.wrap(randomRow).as('selectedRow')
        })

        // Kliknutie na "Editovať"
        cy.get('@selectedRow').within(() => {
            cy.contains('Editovať').click()
        })

        // Generovanie náhodného mena

        const randomFirstName = firstNames[Math.floor(Math.random() * firstNames.length)]
        const randomLastName = lastNames[Math.floor(Math.random() * lastNames.length)]
        const randomStudentEmail = `cypress.test.${Date.now()}@student.ukf.sk`
        const randomPhone = `+421${Math.floor(Math.random() * 900000000 + 100000000)}`
        const randomPersonalEmail = `cypress.test.${Date.now()}@gmail.com`
        const randomHouseNumber = Math.floor(Math.random() * 200 + 1)
        const randomAddress = `Hlavná ${randomHouseNumber}/1, Komárno, 946 01`

        // Kontrola cesty
        cy.url().should('include', '/dashboard/admin/students/edit/')

        // Zmena mena
        cy.get('#input-v-1-1').clear().type(randomFirstName) // meno
        cy.get('#input-v-1-4').clear().type(randomLastName) // priezvisko

        // Zmena e-mailu
        cy.get('#input-v-1-7').clear().type(randomStudentEmail)

        // Zmena telefónu
        cy.get('#input-v-1-10').clear().type(randomPhone)

        // Zmena študijného programu
        cy.get('#input-v-1-13').clear().type('aplikovaná informatika')

        // Zmena osobného e-mailu
        cy.get('#input-v-1-16').clear().type(randomPersonalEmail)

        // Zmena adresy
        cy.get('#input-v-1-19').clear().type(randomAddress)

        // Uložiť zmeny
        cy.contains('Uložiť zmeny').click()

        // Počkanie na uloženie
        cy.wait(2000)
        cy.url().should('include', '/dashboard/admin/students')

        // Overenie zmien v tabuľke
        cy.get('@selectedIndex').then((index) => {
            cy.get('table tbody tr').eq(index as number).within(() => {
                const expectedValues = [
                    `${randomFirstName} ${randomLastName}`,
                    randomStudentEmail,
                    randomPhone,
                    'aplikovaná informatika',
                    randomPersonalEmail,
                    randomAddress
                ]

                cy.get('td').then($cells => {
                    expectedValues.forEach((expectedValue, i) => {
                        expect($cells.eq(i).text().trim()).to.equal(expectedValue)
                    })
                })
            })
        })
    })
})