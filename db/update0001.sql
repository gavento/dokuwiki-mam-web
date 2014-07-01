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

CREATE TYPE pohlavi_enum AS ENUM ('m', 'f');

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





CREATE TYPE typ_problemu_enum AS ENUM ('uloha', 'tema', 'serial', 'org-clanek', 'res-clanek');

CREATE TABLE problemy (
    -- interní MaM id
    id SERIAL PRIMARY KEY,

    -- [WD] Typ: úloha/téma/...
    typ typ_problemu_enum NOT NULL,

    -- [implicitní] stránka úlohy na wiki - zdroj dat v /org/p/
    pageid text UNIQUE NOT NULL,

    -- [WD] číslo úlohy v čísle, např 4 pro u2.4, jen pro úlohu
    cislo_ulohy integer CHECK ((cislo_ulohy IS NULL) OR (typ = 'uloha')),

    -- [WD] číslo úlohy v čísle, např 4 pro u2.4, jen pro úlohu
    cislo_tematu integer CHECK ((cislo_tematu IS NULL) OR (typ = 'tema')),

    -- [WD] název bez čísla, např 'Poissonova'
    nazev text NOT NULL,

    -- [WD] Příznak smazání z návrhů (bude archivováno)
    -- Publikované se nikdy nemažou
    smazany boolean NOT NULL DEFAULT false,
    CHECK ((NOT smazany) OR ((verejne_pageid IS NULL) AND (verejne_cislo IS NULL))),

    -- [WD] maximální počet bodů za úlohu
    -- v případě tématu, seriálu a článků NULL
    body integer,

    -- [WD] pokud je k úloze/téma tu zveřejněno zadání / řešení, pak:
    verejne_pageid text,

    -- [WD] ve kterém čísle se objevilo zadání úlohy (takto se získá její deadline)
    -- nebo první výskyt seriálu, článku, tématu (to ale deadline nemá)
    -- NULL dokud jde jen o návrh.
    verejne_cislo integer REFERENCES cisla(id),

    -- [WD] ve kterém čísle se mají započítat body za úlohu a objevilo se řešení
    -- v případě tématu a jiných NULL
    reseni_cislo integer REFERENCES cisla(id),
    CHECK ((reseni_cislo IS NULL) OR (typ = 'uloha')),

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
    datum_vytvoreni date NOT NULL
);

CREATE UNIQUE INDEX problemy_pageid_idx ON problemy (pageid);

CREATE UNIQUE INDEX problemy_verejne_pageid_idx ON problemy (verejne_pageid);

CREATE TYPE stav_problemu_enum AS ENUM ('navrh', 'verejny', 'smazany');

-- Funkce pro indikaci stavu problému, používá se jako:
-- SELECT *, p.stav FROM problemy p WHERE ...;
CREATE FUNCTION stav(problemy)
  RETURNS stav_problemu_enum STABLE LANGUAGE SQL AS
$BODY$
    SELECT CASE WHEN (p.smazany)
                  THEN 'smazany'::stav_problemu_enum
                WHEN ((p.verejne_pageid IS NOT NULL) AND (p.verejne_cislo IS NOT NULL))
                  THEN 'verejny'::stav_problemu_enum
                ELSE 'navrh'::stav_problemu_enum
           END
    FROM   problemy p
    WHERE  p.id = $1.id;
$BODY$;


CREATE TABLE reseni (
    -- interní MaM id
    id SERIAL PRIMARY KEY,

    resitel INTEGER NOT NULL REFERENCES resitele(id),
    problem INTEGER NOT NULL REFERENCES problemy(id),

    -- Pokud je NULL, úloha ještě nebyla opravena
    ziskane_body INTEGER,

    -- ve kterém čísle mají být za řešení uděleny body
    cislo INTEGER REFERENCES cisla(id),

    -- komentář / log (kdy a kým bylo vyzvednuto ...)
    komentar text,
    -- forma řešení
    papirove BOOLEAN NOT NULL,

    -- (volitelná) stránka s tímto řešením, týká se potenciálně témat a konfer
    pageid text
);

CREATE INDEX reseni_resitel_idx ON reseni (resitel);

CREATE INDEX reseni_problem_idx ON reseni (problem);

CREATE INDEX reseni_resitel_problem_idx ON reseni (resitel, problem);


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
    misto text NOT NULL
);

CREATE UNIQUE INDEX soustredeni_pageid_idx ON soustredeni (pageid);

CREATE TABLE ucasti (
    -- relace mezi ucastniky a soustredky
    soustredeni INTEGER REFERENCES soustredeni,
    resitel INTEGER REFERENCES resitele,

    -- volitelne data ucasti
    zacatek DATE,
    konec DATE,

    PRIMARY KEY (soustredeni, resitel)
);

CREATE INDEX ucasti_soustredeni_idx ON ucasti (soustredeni);

CREATE INDEX ucasti_resitel_idx ON ucasti (resitel);


CREATE TABLE verze_db (
     -- Nejvetsi verze je aktualni
     verze INTEGER,
     datum TIMESTAMP
);

