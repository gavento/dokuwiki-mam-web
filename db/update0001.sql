CREATE TABLE skoly (
    -- interní id v MaM
    id SERIAL PRIMARY KEY,
 
    -- aesopi ID
    id_aesop text,
 
    -- IZO školy (jen české školy)
    izo text,
 
    -- Celý název školy
    nazev text NOT NULL,
 
    -- Zkraceny nazev pro zobrazení ve výsledkovce, volitelné.
    -- Není v Aesopovi, musíme vytvářet sami.
    kratky_nazev text,
 
    -- Adresa
    -- Ulice může být jen číslo
    ulice text NOT NULL,
    mesto text NOT NULL,
    psc text NOT NULL,
    -- ISO 3166-1 dvojznakovy kod zeme velkym pismem (CZ, SK)
    stat text NOT NULL
);
 
CREATE INDEX skoly_id_aesop_idx ON skoly (id_aesop);

-- možnost 'nikam' je tu pro případ, kdy se řešitel přestěhuje a nedá nám novou
-- adresu. Prý se stalo.
CREATE TYPE kam_posilat_enum AS ENUM ('domu', 'do_skoly', 'nikam');
 
CREATE TYPE pohlaví_enum AS ENUM ('m', 'f');
 
CREATE TABLE resitele (
 
    id SERIAL PRIMARY KEY,
    jmeno text NOT NULL,
    prijmeni text NOT NULL,
 
    -- má uživatel wiki-účet? 
    -- řešitelé obecně nemusejí být registrováni na wiki
    username text, -- username
 
    -- 'f'/'m', asi se může stát, že ho neznáme
    pohlavi pohlavi_enum,
 
    -- škola
    skola INTEGER REFERENCES skoly(id),
 
    -- Očekávaný rok maturity
    rok_maturity INTEGER,
 
    -- Kontakty a detaily, pokud známe
    email text,
    telefon text,
    datum_narozeni DATE,

    -- NULL dokud nedali souhlas
    datum_souhlasu DATE, -- se zpracováním osobních údajů
    datum_souhlasu_spam DATE, -- se zasíláním fakultních informací
 
    -- připojení řešitele k MaM
    datum_vytvoreni DATE NOT NULL,
 
    -- kam zasílat papírové řešení
    kam_posilat kam_posilat_enum NOT NULL,
 
    -- Adresa podobně jako u škol, nemusí být známa pokud
    -- posíláme na školu.
    ulice text,
    mesto text,
    psc text,
    -- ISO 3166-1 dvojznakovy kod zeme velkym pismem (CZ, SK)
    stat text
);
 
CREATE INDEX resitele_username_idx ON resitele (username);
 
CREATE INDEX resitele_skola_idx ON resitele (skola);


CREATE TABLE cisla (
    -- interní MaM id
    id SERIAL PRIMARY KEY,
 
    -- 1..21..
    rocnik INTEGER NOT NULL,
 
    -- 1..8, "speciální" vydání (DOD, ...) se nepočítají
    cislo INTEGER NOT NULL,
 
    -- datum finalni verze, NULL dokud není vydáno
    datum_vydani DATE,
 
    -- číslo se stává veřejným (pro řešitele) ve chvíli, kdy je připraveno k
    -- tisku.
    verejne BOOLEAN NOT NULL,
 
    -- platí pro úlohy zadané v tomto čísle
    deadline TIMESTAMP
);

CREATE TYPE typ_ulohy AS ENUM ('uloha', 'tema');
 
CREATE TABLE ulohy (
    -- interní MaM id
    id SERIAL PRIMARY KEY,
 
    -- [implicitní] stránka úlohy na wiki - zdroj dat
    pageid text UNIQUE NOT NULL,
 
    -- [WD] název bez čísla, např 'Poissonova'
    nazev text NOT NULL,
 
    -- [WD] Typ: úloha/téma
    typ typ_ulohy NOT NULL,
 
    -- [WD] Příznak smazání z návrhů (bude archivováno)
    -- Publikované se nikdy nemažou
    smazano BOOLEAN NOT NULL,
 
    -- [WD] maximální počet bodů za úlohu
    -- v případě tématu NULL
    body INTEGER,
 
    -- [WD] pokud je k úloze/téma tu zveřejněno zadání / řešení, pak:
    verejne_pageid text,
 
    -- [WD] ve kterém čísle se objevilo zadání úlohy (takto se získá její deadline)
    -- podobně v případě tématu, to ale deadline nemá
    -- NULL dokud jde jen o návrh
    cislo_zadani INTEGER REFERENCES cisla(id),
 
    -- [WD] číslo úlohy v čísle, např 4 pro u2.4
    cislo_ulohy INTEGER,
 
    -- [WD] ve kterém čísle se mají za úlohu započítat body
    -- v případě tématu NULL
    cislo_reseni INTEGER REFERENCES cisla(id),
 
    -- [WD] Kdo úlohu zadal
    zadavatel text NOT NULL, -- username
 
    -- [WD] Kdo je přiřazen jako opravovatel
    opravovatel text, -- username
 
    -- podmnožina {M, F, I, B}
    ---- kategorie bit(4) NOT NULL, -- ???
    -- gavento: Navrhuji zrušit a nahradit dokuwiki tags na úlohy
    --          (tedy krom M/F/I i klíčová slova a pod)
    --          Není jasné, zda pluginem tags, nebo nějak sami ...
 
    -- [implicitní]
    datum_vytvoreni DATE NOT NULL,
);
 
CREATE UNIQUE INDEX ulohy_pageid_idx ON ulohy (pageid);
 
CREATE UNIQUE INDEX ulohy_verejne_pageid_idx ON ulohy (verejne_pageid);


CREATE TABLE reseni (
    -- interní MaM id
    id SERIAL PRIMARY KEY,
 
    resitel INTEGER NOT NULL REFERENCES resitele(id),
    uloha_tema INTEGER NOT NULL REFERENCES ulohy_temata(id),
 
    -- Pokud je NULL, úloha ještě nebyla opravena
    ziskane_body INTEGER,
 
    -- ve kterém čísle mají být za řešení uděleny body
    -- (pro úlohu by se to dalo zjistit přes uloha_tema -> cislo_zadani -> deadline, ale pro téma ne)
    cislo_body INTEGER REFERENCES cisla(id),
 
    -- komentář / log (kdy a kým bylo vyzvednuto ...)
    komentar text,
    -- forma řešení
    papirove BOOLEAN NOT NULL,
 
    -- (volitelná) stránka s tímto řešením, týká se potenciálně témat a konfer
    pageid text,
);
 
CREATE INDEX reseni_resitel_idx ON reseni (resitel);
 
CREATE INDEX reseni_uloha_idx ON reseni (uloha);
 
CREATE INDEX reseni_resitel_uloha_idx ON reseni (resitel, uloha);


CREATE TABLE soustredeni ( -- WikiData
    -- interní DB id
    id SERIAL PRIMARY KEY,
 
    -- [implicitní] stránka úlohy na wiki - zdroj dat
    pageid text UNIQUE NOT NULL,
 
    -- [WD] přesnost na dny
    zacatek DATE NOT NULL,
 
    -- [WD] přesnost na dny
    konec DATE NOT NULL,
 
    -- [WD] jméno obce
    misto text NOT NULL,
);
 
CREATE UNIQUE INDEX soustredeni_pageid_idx ON soustredeni (pageid);

CREATE TABLE ucasti (
    -- relace mezi ucastniky a soustredky
    soustredeni INTEGER REFERENCES soustredeni,
    resitel INTEGER REFERENCES resitele,
 
    -- volitelne data ucasti
    od DATE,
    do DATE,
 
    PRIMARY KEY (soustredeni, resitel)
);
 
CREATE INDEX ucasti_soustredeni_idx ON ucasti (soustredeni);
 
CREATE INDEX ucasti_resitel_idx ON ucasti (resitel);


CREATE TABLE verze_db (
     -- Nejvetsi verze je aktualni
     verze INTEGER;
);

INSERT INTO verze_db VALUES (1);

