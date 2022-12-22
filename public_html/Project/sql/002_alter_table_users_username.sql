ALTER TABLE Users ADD COLUMN username varchar(30) 
not null unique
COMMENT 'Username field that defaults to the name of the email given';