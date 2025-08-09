BEGIN;

-- --------------------------------------------------------
-- Database objects for Brain-Buzz converted to PostgreSQL
-- --------------------------------------------------------

-- Table: dept
CREATE TABLE dept (
  dept_id INTEGER PRIMARY KEY,
  dept_name VARCHAR(3)
);

INSERT INTO dept (dept_id, dept_name) VALUES
(1, 'CSE'),
(2, 'ISE'),
(3, 'ECE'),
(4, 'EEE');

-- Table: quiz
CREATE TABLE quiz (
  quizid SERIAL PRIMARY KEY,
  quizname VARCHAR(100) NOT NULL,
  date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  staffid VARCHAR(30) NOT NULL
);

-- Insert the sample row with explicit id; set sequence later
INSERT INTO quiz (quizid, quizname, date_created, staffid) VALUES
(29, 'cyber security', '2024-12-27 17:43:34', '101');

-- Table: student
CREATE TABLE student (
  usn VARCHAR(10) PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  mail VARCHAR(255) NOT NULL,
  phno VARCHAR(20) NOT NULL,
  gender VARCHAR(1) NOT NULL,
  DOB VARCHAR(20) NOT NULL,
  pw VARCHAR(200) NOT NULL,
  dept VARCHAR(3)
);

INSERT INTO student (usn, name, mail, phno, gender, DOB, pw, dept) VALUES
('4SC22CS022', 'Student', 'student@sahyadri.com', '8786788909', 'M', '2001-01-08', 'student@123', 'CSE');

-- Table: staff
CREATE TABLE staff (
  staffid VARCHAR(10) PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  mail VARCHAR(255) NOT NULL,
  phno VARCHAR(20) NOT NULL,
  gender VARCHAR(1) NOT NULL,
  DOB VARCHAR(20) NOT NULL,
  pw VARCHAR(200) NOT NULL,
  dept VARCHAR(3)
);

INSERT INTO staff (staffid, name, mail, phno, gender, DOB, pw, dept) VALUES
('101', 'Teacher', 'staff@sahyadri.com', '9878987878', 'M', '1992-01-06', 'password', 'ISE');

-- Table: questions
CREATE TABLE questions (
  question_id SERIAL PRIMARY KEY,
  qs VARCHAR(200) NOT NULL UNIQUE,
  op1 VARCHAR(255) NOT NULL,
  op2 VARCHAR(255) NOT NULL,
  op3 VARCHAR(255) NOT NULL,
  answer VARCHAR(255) NOT NULL,
  quizid INTEGER NOT NULL
);

-- Insert questions with explicit ids (we'll set nextval later)
INSERT INTO questions (question_id, qs, op1, op2, op3, answer, quizid) VALUES
(15, 'Which of the following usually observe each activity on the internet of the victim, gather all information in the background, and send it to someone else?', 'Malware', 'Spyware', 'Adware', 'All of the above', 29),
(16, 'Which one of the following is a type of antivirus program?', 'Quick heal', 'Mcafee', 'Kaspersky', 'All of the above', 29),
(17, 'Which one of the following usually used in the process of Wi-Fi-hacking?', 'Wireshark', 'Norton', 'Nmap', 'Aircrack-ng', 29),
(18, 'Which of these is a standard interface for serial data transmission?', 'ASCII', 'Centronics', '2', 'RS232C', 29),
(19, 'Which type of topology is best suited for large businesses which must carefully control and coordinate the operation of distributed branch outlets?', 'Ring', 'Local area', 'Hierarchical', 'Star', 29),
(20, 'Parity bits are used for which of the following purposes?', 'Encryption of data', 'To transmit faster', 'To identify the user', 'To detect errors', 29),
(21, 'Which sports person is nick named Dennis the Menace?', 'B John McEnroe', 'Sampras', 'C Jim Courier', 'A Pete Sampras', 31);

-- Table: score
CREATE TABLE score (
  slno SERIAL PRIMARY KEY,
  score INTEGER NOT NULL,
  quizid INTEGER NOT NULL,
  usn VARCHAR(30) NOT NULL,
  totalscore INTEGER NOT NULL,
  remark VARCHAR(20) NOT NULL
);

INSERT INTO score (slno, score, quizid, usn, totalscore, remark) VALUES
(34, 1, 29, '4sf22cs54', 6, 'good'),
(35, 1, 31, '4sf22cs54', 1, 'good'),
(36, 0, 29, '4SC22CS022', 6, 'bad'),
(37, 4, 29, '4SC22CS022', 6, 'bad'),
(38, 5, 29, '4SC22CS022', 6, 'good');

-- Foreign key constraints
ALTER TABLE questions
  ADD CONSTRAINT questions_quizid_fk FOREIGN KEY (quizid) REFERENCES quiz (quizid) ON DELETE CASCADE;

ALTER TABLE score
  ADD CONSTRAINT score_quizid_fk FOREIGN KEY (quizid) REFERENCES quiz (quizid) ON DELETE CASCADE,
  ADD CONSTRAINT score_usn_fk FOREIGN KEY (usn) REFERENCES student (usn);

-- ------------------------------------------------------------------
-- Triggers and trigger functions (converted to PostgreSQL PL/pgSQL)
-- ------------------------------------------------------------------

-- Trigger function: delete questions when quiz deleted (ondeleteqs)
CREATE OR REPLACE FUNCTION fn_delete_questions_on_quiz_delete()
RETURNS trigger AS $$
BEGIN
  DELETE FROM questions WHERE questions.quizid = OLD.quizid;
  RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER ondeleteqs
AFTER DELETE ON quiz
FOR EACH ROW
EXECUTE FUNCTION fn_delete_questions_on_quiz_delete();

-- Trigger function: assert_unique_quizname (before insert)
CREATE OR REPLACE FUNCTION fn_assert_unique_quizname()
RETURNS trigger AS $$
BEGIN
  IF (SELECT COUNT(*) FROM quiz WHERE quizname = NEW.quizname) > 0 THEN
    RAISE EXCEPTION 'Assertion failed: Quiz name must be unique.';
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER assert_unique_quizname
BEFORE INSERT ON quiz
FOR EACH ROW
EXECUTE FUNCTION fn_assert_unique_quizname();

-- Trigger function: remarks (before insert on score) to set remark column
CREATE OR REPLACE FUNCTION fn_set_remark_on_score_insert()
RETURNS trigger AS $$
BEGIN
  IF NEW.score < 5 THEN
    NEW.remark := 'bad';
  ELSE
    NEW.remark := 'good';
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER remarks
BEFORE INSERT ON score
FOR EACH ROW
EXECUTE FUNCTION fn_set_remark_on_score_insert();

-- ------------------------------------------------------------------
-- Leaderboard: convert procedure to function returning setof rows
-- ------------------------------------------------------------------
CREATE OR REPLACE FUNCTION leaderboard()
RETURNS TABLE (
  quizname VARCHAR,
  score INTEGER,
  totalscore INTEGER,
  usn VARCHAR,
  name VARCHAR
) AS $$
BEGIN
  RETURN QUERY
  SELECT q.quizname, s.score, s.totalscore, st.usn, st.name
  FROM score s
  JOIN student st ON s.usn = st.usn
  JOIN quiz q ON q.quizid = s.quizid
  ORDER BY s.score DESC;
END;
$$ LANGUAGE plpgsql;

-- ------------------------------------------------------------------
-- Fix sequences for SERIAL columns after explicit inserts
-- ------------------------------------------------------------------

-- Set quiz sequence to max(quizid)+1
DO $$
DECLARE
  maxq INT;
BEGIN
  SELECT COALESCE(MAX(quizid), 0) INTO maxq FROM quiz;
  IF maxq IS NULL THEN
    maxq := 1;
  END IF;
  EXECUTE format('ALTER SEQUENCE quiz_quizid_seq RESTART WITH %s;', maxq + 1);
EXCEPTION WHEN undefined_table THEN
  -- if sequence name is different (older PG versions) attempt to create sequence
  RAISE NOTICE 'Sequence quiz_quizid_seq not found; skipping restart.';
END;
$$;

-- Set questions sequence to max(question_id)+1
DO $$
DECLARE
  maxq INT;
BEGIN
  SELECT COALESCE(MAX(question_id), 0) INTO maxq FROM questions;
  IF maxq IS NULL THEN
    maxq := 1;
  END IF;
  EXECUTE format('ALTER SEQUENCE questions_question_id_seq RESTART WITH %s;', maxq + 1);
EXCEPTION WHEN undefined_table THEN
  RAISE NOTICE 'Sequence questions_question_id_seq not found; skipping restart.';
END;
$$;

-- Set score sequence to max(slno)+1
DO $$
DECLARE
  maxs INT;
BEGIN
  SELECT COALESCE(MAX(slno), 0) INTO maxs FROM score;
  IF maxs IS NULL THEN
    maxs := 1;
  END IF;
  EXECUTE format('ALTER SEQUENCE score_slno_seq RESTART WITH %s;', maxs + 1);
EXCEPTION WHEN undefined_table THEN
  RAISE NOTICE 'Sequence score_slno_seq not found; skipping restart.';
END;
$$;

COMMIT;