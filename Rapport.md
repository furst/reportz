# Rapport

## Användbara länkar med info

* Repo: https://github.com/furst/reportz
* Testfall: https://github.com/furst/reportz/blob/master/use-cases.md
* Testrapport: http://flexboard.se/view-report/Reportz/af222ht529f7b87cde5a
* Klassdiagram: http://yuml.me/86311a5e (det är en dubbelpil på ett ställe som ska bort)
* Dokumentation: https://github.com/furst/reportz/blob/master/releaseinfo.md

## Komma åt applikationen

* Gå in på www.flexboard.se
* Logga in med "adde" och "abc123"

## Om projektet

Grundtanken var att hjälpa dig med dina testrapporter i kursen. Där man enkelt kan skapa rapporter och tillhörande testfall. Man får en bra överblick över varje rapport och kan enkelt hitta alla ifyllda rapporter med tillhörande data. Det underlättar även för eleverna då man får ett mail med en länk/kod som man kan använda för att redigera sin rapport utan att behöva fylla i en ny. Det går även bra att bara fylla i en del av rapporten för att sedan spara den ochh återkomma till. För att enkelt kunna återanvända en rapport har jag lagt till en dupliceringsfunktion som kopierar en rapport och alla dess testfall.

Jag har använt mig av slim-framework för att framförallt sköta routingen. Det är enkelt att använda men kan lätt kladda upp applikationen, därför blev inte indexfilen så vacker. Varje url som hittas i index.php kör i sin tur en metod i en controller som hämtar och renderar en vyer.

Jag använder mig av templates för att göra vyrenderingen lättare, detta har medfört att jag får lite strängberoenden mellan controller och templates. En fördel är dock att applikationen inte kraschar när en variabel inte hittas i templaten, den variabeln renderas bara inte.

Jag har använt mig av sessioner i controller som inte verkar vara helt populärt, dock är det endast för meddelanden och för att återpopulera fält vid fel.

ReportControllern blev en aning stor men det fungerar bra tycker jag så slipper jag hoppa så mycket mellan två eller flera controllrar.

Eftersom jag använder mig av templates kunde jag inte heller spara meddelanden där. För att slippa att ha meddelanden direkt i controllern gjorde jag en vyklass som håller de olika meddelanden. Valideringsmeddelanden hämtas också från en valideringmodell, jag kände att det var okej eftersom meddelanden kan klassas som data, framförallt de som måste innehålla en kolumns namn.

Jag försökte skapa en klass för att hålla en connection, så jag smidigt kunde kalla sql-satser utan en massa duplicering. Den blev helt okej men gick inte att använda på alla platser så jag fick göra en fullösning med en "andra connection". Koden blev åtminstone lite vackrare.

Om du känner att applikationen är något du är sugen på att använda i framtida kurser är det bara att hojta, kanske behövs komplettera med något fält i databasen?
