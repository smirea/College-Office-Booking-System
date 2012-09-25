CREATE TABLE items(
  id          int(8) auto_increment,
  type        varchar(32),
  timestamp   int(16),
  item        varchar(64),
  booked      varchar(32),
  returned    varchar(32),
  user        varchar(64),
  phone       varchar(16),
  email       varchar(64),
  checkOutBy  varchar(64),
  checkInBy   varchar(64),
  PRIMARY KEY(id),
  INDEX(item),
  INDEX(timestamp),
  INDEX(item),
  INDEX(booked),
  INDEX(returned),
  INDEX(user),
  INDEX(email),
  INDEX(phone),
  INDEX(checkOutBy),
  INDEX(checkInBy)
);

CREATE VIEW bulk                as SELECT * FROM items WHERE type = '' AND returned = '';
CREATE VIEW bulkReturned        as SELECT * FROM items WHERE type = '' AND returned <> '';
CREATE VIEW boardGames          as SELECT * FROM items WHERE type = 'boardGame' AND returned = '';
CREATE VIEW books               as SELECT * FROM items WHERE type = 'book' AND returned = '';
CREATE VIEW soundSystem         as SELECT * FROM items WHERE type = 'soundSystem';
CREATE VIEW beamer              as SELECT * FROM items WHERE type = 'beamer';
CREATE VIEW conferenceRoomKeys  as SELECT * FROM items WHERE type = 'conferenceRoomKey';