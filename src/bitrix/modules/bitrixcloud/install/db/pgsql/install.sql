
CREATE TABLE b_bitrixcloud_option (
  ID int GENERATED BY DEFAULT AS IDENTITY NOT NULL,
  NAME varchar(50) NOT NULL,
  SORT int NOT NULL,
  PARAM_KEY varchar(50),
  PARAM_VALUE varchar(200),
  PRIMARY KEY (ID)
);
CREATE INDEX ix_b_bitrixcloud_option_name ON b_bitrixcloud_option (name);