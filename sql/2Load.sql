-- Load STUDENT data
LOAD DATA INFILE '/var/lib/mysql-files/STUDENT.csv'
-- Specify the table name
INTO TABLE Project_Finder.STUDENT
-- each entity (pwd, netID, etc) separated by a ',' w/no spaces
FIELDS TERMINATED BY ','
-- Each field enclosed by quotes- these are delimiters not data 
ENCLOSED BY '"'
-- One entry per line
LINES TERMINATED BY '\n'
-- Skip Headers
IGNORE 1 ROWS;

-- Load STUDENT_SOCIALS data
-- Same/Similar structure for all data 
LOAD DATA INFILE '/var/lib/mysql-files/STUDENT_SOCIALS.csv'
INTO TABLE Project_Finder.STUDENT_SOCIALS
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Load PROJECT data
LOAD DATA INFILE '/var/lib/mysql-files/PROJECT.csv'
INTO TABLE Project_Finder.PROJECT
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Load PROJECT_CODING_LANGUAGES data
LOAD DATA INFILE '/var/lib/mysql-files/PROJECT_CODING_LANGUAGES.csv'
INTO TABLE Project_Finder.PROJECT_CODING_LANGUAGES
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Load WORKS_ON data
LOAD DATA INFILE '/var/lib/mysql-files/WORKS_ON.csv'
INTO TABLE Project_Finder.WORKS_ON
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Load INFRACTION data
LOAD DATA INFILE '/var/lib/mysql-files/INFRACTION.csv'
INTO TABLE Project_Finder.INFRACTION
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Load ASSOCIATED_WITH data
LOAD DATA INFILE '/var/lib/mysql-files/ASSOCIATED_WITH.csv'
INTO TABLE Project_Finder.ASSOCIATED_WITH
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;
