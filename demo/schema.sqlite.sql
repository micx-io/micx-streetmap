CREATE TABLE city (
    city_id INTEGER PRIMARY KEY,
    postcode TEXT NOT NULL,
    city TEXT NOT NULL,
    icity TEXT NOT NULL
);

CREATE INDEX city_postal_idx ON city (postcode);
CREATE INDEX city_icity_idx ON city (icity);

CREATE TABLE street (
	street_id INTEGER PRIMARY KEY,
	street_name TEXT NOT NULL,
	istreet_name TEXT NOT NULL,
	postcode TEXT NOT NULL,
	city TEXT NOT NULL,
	icity TEXT NOT NULL
);

CREATE INDEX street_istreet_idx ON street (istreet_name);
CREATE INDEX street_icity_idx ON street (icity);
CREATE INDEX street_postcode_idx ON street (postcode);

CREATE TABLE housenr (
    street_id TEXT NOT NULL,
    housenr TEXT NOT NULL
);
CREATE INDEX housenr_idx ON housenr (street_id, housenr);

