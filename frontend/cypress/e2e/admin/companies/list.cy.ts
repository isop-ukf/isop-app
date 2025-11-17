describe('Admin Company CRUD', () => {
    beforeEach(() => {
        cy.visit('/login')

        cy.wait(3000)

        cy.get('input[type="email"]').type('test@example.com')
        cy.get('input[type="password"]').type('password')
        cy.get('button[type="submit"]').click()

        cy.wait(1000)

        cy.contains("Firmy").click()
        cy.url().should('include', '/dashboard/admin/companies')
    })

    it('should load the list of companies in a proper format', () => {
        cy.get('table').within(() => {
            cy.validateColumn('Názov', (name) => {
                expect(name).to.not.be.empty
            })

            cy.validateColumn('IČO', (ico) => {
                expect(ico.trim()).to.not.be.empty
                expect(Number(ico.trim())).to.be.a('number').and.not.be.NaN
            })

            cy.validateColumn('Kontaktná osoba', (contact_name) => {
                const nameParts = contact_name.trim().split(' ')
                expect(nameParts).to.have.length.at.least(2)
                expect(nameParts[0]).to.not.be.empty
                expect(nameParts[1]).to.not.be.empty
            })

            cy.validateColumn('Telefón', (program) => {
                expect(program.trim()).to.not.be.empty
            })

            cy.validateColumn('E-mail', (email) => {
                expect(email.trim()).to.not.be.empty
                expect(email.trim()).to.include("@")
            })

            cy.validateColumn('Prijímajú študentov', (hiring) => {
                expect(hiring.trim()).to.be.oneOf(['Áno', 'Nie'])
            })

            cy.contains('th', 'Operácie').parent('tr')
        })
    })

    it('should be able to delete a company', () => {
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
        const companyNames = [
            'Tech Solutions s.r.o.',
            'Digital Systems a.s.',
            'Innovation Labs s.r.o.',
            'Software House Slovakia',
            'Data Analytics Group',
            'Cloud Services s.r.o.',
            'IT Consulting a.s.',
            'Web Development Studio',
            'Mobile Apps Company',
            'Cyber Security s.r.o.'
        ]

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

        const randomCompanyName = companyNames[Math.floor(Math.random() * companyNames.length)]
        const randomHouseNumber = Math.floor(Math.random() * 200 + 1)
        const randomAddress = `Hlavná ${randomHouseNumber}/1, Komárno, 946 01`
        const randomICO = String(Math.floor(Math.random() * 90000000000) + 10000000000)

        // Kontrola cesty
        cy.url().should('include', '/dashboard/admin/companies/edit/')

        // Zmena názvu
        cy.get('#input-v-1-1').clear().type(randomCompanyName)

        // Zmena adresy
        cy.get('#input-v-1-4').clear().type(randomAddress)

        // Zmena IČO
        cy.get('#input-v-1-7').clear().type(randomICO)

        // Uložiť zmeny
        cy.contains('Uložiť zmeny').click()

        // Počkanie na uloženie
        cy.wait(2000)
        cy.url().should('include', '/dashboard/admin/companies')

        // Overenie zmien v tabuľke
        cy.get('@selectedIndex').then((index) => {
            cy.get('table tbody tr').eq(index as number).within(() => {
                const expectedValues = [
                    `${randomCompanyName}`,
                    randomICO,
                    null, // netestuje sa
                    null, // netestuje sa
                    null, // netestuje sa
                    null // netestuje sa
                ]

                cy.get('td').then($cells => {
                    expectedValues.forEach((expectedValue, i) => {
                        if (expectedValue === null) return; // skip checking
                        expect($cells.eq(i).text().trim()).to.equal(expectedValue)
                    })
                })
            })
        })
    })
})