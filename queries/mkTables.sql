CREATE TABLE activiteit (
    activiteit_id INT NOT NULL,
    naam VARCHAR(50) NOT NULL,
    periode INT NOT NULL,
    actief TEXT NOT NULL,
    PRIMARY KEY (activiteit_id)
);

CREATE TABLE medewerker (
    medewerker_id INT NOT NULL,
    naam VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    groep INT NOT NULL,
    actief TEXT NOT NULL,
    PRIMARY KEY (medewerker_id)
);

CREATE TABLE urenregistratie (
    urenregistratie_id INT NOT NULL AUTO_INCREMENT,
    medewerker_id INT NOT NULL,
    datum DATE NOT NULL,
    activiteit_id INT NOT NULL,
    minuten INT NOT NULL,
    PRIMARY KEY (urenregistratie_id),
    FOREIGN KEY (medewerker_id) REFERENCES medewerker(medewerker_id),
    FOREIGN KEY (activiteit_id) REFERENCES activiteit(activiteit_id)
);