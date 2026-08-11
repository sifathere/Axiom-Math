CREATE DATABASE IF NOT EXISTS axiommath_db;
USE axiommath_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---- Table: categories ----
CREATE TABLE IF NOT EXISTS categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(50) NOT NULL UNIQUE,
  icon VARCHAR(10)
) ENGINE=InnoDB;

-- ---- Table: formulas ----
CREATE TABLE IF NOT EXISTS formulas (
  formula_id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  title VARCHAR(120) NOT NULL,
  expression VARCHAR(255) NOT NULL,
  description TEXT,
  FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---- Table: practice_problems ----
CREATE TABLE IF NOT EXISTS practice_problems (
  problem_id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  question VARCHAR(255) NOT NULL,
  difficulty ENUM('Easy', 'Medium', 'Hard') DEFAULT 'Medium',
  hint TEXT,
  answer VARCHAR(100) NOT NULL,
  FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---- Table: user_progress ----
CREATE TABLE IF NOT EXISTS user_progress (
  progress_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  problem_id INT NOT NULL,
  is_correct BOOLEAN DEFAULT FALSE,
  attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (problem_id) REFERENCES practice_problems(problem_id) ON DELETE CASCADE
) ENGINE=InnoDB;

DELETE FROM user_progress;
DELETE FROM practice_problems;
DELETE FROM formulas;
DELETE FROM categories;


ALTER TABLE user_progress AUTO_INCREMENT = 1;
ALTER TABLE practice_problems AUTO_INCREMENT = 1;
ALTER TABLE formulas AUTO_INCREMENT = 1;
ALTER TABLE categories AUTO_INCREMENT = 1;

INSERT INTO categories (category_id, category_name, icon) VALUES
  (1, 'Algebra', '📘'),
  (2, 'Geometry', '📗'),
  (3, 'Calculus', '📙'),
  (4, 'Trigonometry', '📕'),
  (5, 'Statistics', '📓'),
  (6, 'Linear Algebra', '📔');

INSERT INTO formulas (category_id, title, expression, description) VALUES
  (1, 'Quadratic Formula', 'x = (-b ± √(b²-4ac)) / 2a', 'Solves any equation of the form ax² + bx + c = 0.'),
  (1, 'Difference of Squares', 'a² - b² = (a+b)(a-b)', 'Factors a squared term minus another squared term.'),
  (2, 'Pythagorean Theorem', 'c² = a² + b²', 'Relates the sides of a right triangle.'),
  (3, 'Power Rule', 'd/dx[xⁿ] = n·xⁿ⁻¹', 'Differentiates any power of x.'),
  (4, 'Law of Sines', 'a/sin(A) = b/sin(B) = c/sin(C)', 'Relates side lengths to opposite angles in any triangle.'),
  (5, 'Mean', 'x̄ = Σx / n', 'The average of a data set.');

INSERT INTO practice_problems (category_id, question, difficulty, hint, answer) VALUES
  (1, 'Solve: 2x² + 5x - 3 = 0', 'Medium', 'Try factoring by grouping. Look for two numbers that multiply to (2 x -3) and add to 5.', 'x = 1/2 or x = -3'),
  (1, 'Factor: x² - 9', 'Easy', 'This is a difference of squares. Think (a+b)(a-b).', '(x+3)(x-3)'),
  (2, 'A right triangle has legs 3 and 4. Find the hypotenuse.', 'Easy', 'Use the Pythagorean theorem: c squared = a squared + b squared.', '5'),
  (3, 'Find d/dx of x³ + 2x', 'Medium', 'Apply the power rule to each term separately.', '3x² + 2');

-- 1) Every formula alongside its category name
SELECT f.title, f.expression, c.category_name
FROM formulas f
INNER JOIN categories c ON f.category_id = c.category_id
ORDER BY c.category_name;

-- 2) Every practice problem, its category, and its hint
SELECT p.question, p.difficulty, c.category_name, p.hint
FROM practice_problems p
INNER JOIN categories c ON p.category_id = c.category_id
ORDER BY p.difficulty;

-- 3) (After seeding user_progress) Which users solved which problems
-- SELECT u.email, pp.question, up.is_correct, up.attempted_at
-- FROM user_progress up
-- INNER JOIN users u ON up.user_id = u.id
-- INNER JOIN practice_problems pp ON up.problem_id = pp.problem_id
-- ORDER BY up.attempted_at DESC;