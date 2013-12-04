# Usecases

## Fully dressed

### UC1 Skapa testrapport

Huvudaktör: programmerare på ett företag

En programmerare på ett företag vill kunna hålla redo på testrapporter till sin applikation.

Förutsätter att användaren är inloggad

1. Användaren klickar på "ny rapport"
2. Systemet visar sidan
3. Användaren fyller i "testrapport" och klickar på spara
4. Systemet sparar datat i databasen och skickar användaren vidare för att lägga till ett testfall
5. Användaren skriver in "navigera till sidan" och "gå till adressen www.google.se" och klickar på "spara testfall"
6. Systemet sparar datat i databasen och skickar användaren vidare för att lägga till ett nytt testfall
7. Användaren klickar på "eller fortsätt till rapport"
8. Systemet visar information om rapporten

#### Extensions
3a. Användaren skriver in en tomsträng
1. Systemet visar valideringsfel

5a. Användaren skriver in en tomsträng
1. Systemet visar valideringsfel

## Brief

### UC2 Logga in
En användare vill logga in och navigerar till logga-in-sidan. Användaren skriver in sina uppgifter och klickar på logga in. Användaren tas till dashboarden. Vid felaktiga uppgifter visas ett felmeddelande.

### UC3 Visa rapport
En inloggad användare klickar på "rapporter" i menyn, där klickar hon på en rapport och systemet visar rapport med tillhörande information.

### UC4 Visa och skicka publik rapport
En icke inloggad användare får en länk till en rapport, den fyller i sina uppgifter och fyller i testfallen och klickar sedan på "skicka raport". Man får bekräftelse och ett mail med information

### UC5 Editera publik rapport
En icke inloggad användare klickar på länken den fick i sitt mail vid en ifylld rapport alternativt fyller i sin kod efter ha klickat på länken "Hämta redan påbörjad rapport". Användaren väljer att ändra ett testfall och klickar sedan på "Skicka rapport"

### UC6 Skriv kommentar
En icke inloggad användare går in på en rapport den fått via en länk. den skriver in sitt namn och en kommentar under ett testfall. Kommentaren visas utan att sidan laddas om. Vid valideringsfel visas ett meddelande

## Casual

### UC7 En inloggad användare vill titta på en ifylld rapport, hen går in på rapporten och klickar på "visa" under en användare under "ifyllda rapporter". Rapporten visas i ett enkelt format.

### UC8 En användare klickar på editera på ett testcase eller en rapport, får upp ett redigerfönster och en spara knapp. Ändrar uppgifter och klickar på spara. Vid valideringsfel visas ett meddelande.

### UC9 En användare vill ta bort ett testcase eller en rapport. Användaren klickar på "ta bort" och föremålet tas bort.




