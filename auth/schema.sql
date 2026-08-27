-- ============================================================
-- AxiomMath — Complete Database Schema
-- One self-contained file: run this on a totally empty MySQL
-- server and it builds the whole database from scratch —
-- database, users table (Week 4), and the formula library /
-- practice problems / progress tracking tables (Week 5).
--
-- Run once in phpMyAdmin: SQL tab → paste this whole file → Go
-- Safe to re-run on a database that already has data: the seed
-- block only clears the 4 Week 5 tables, it never touches
-- `users` or your existing registered test accounts.
-- ============================================================

CREATE DATABASE IF NOT EXISTS axiommath_db;
USE axiommath_db;

-- ---- Table: users (Week 4) ----
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---- Table: categories ----
-- The six subjects shown on the homepage/about page.
CREATE TABLE IF NOT EXISTS categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(50) NOT NULL UNIQUE,
  icon VARCHAR(10)
) ENGINE=InnoDB;

-- ---- Table: formulas ----
-- Each formula belongs to exactly one category (1NF/2NF/3NF: every
-- non-key column describes the formula itself, nothing transitive).
CREATE TABLE IF NOT EXISTS formulas (
  formula_id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  title VARCHAR(120) NOT NULL,
  expression VARCHAR(255) NOT NULL,
  description TEXT,
  FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---- Table: practice_problems ----
-- Practice questions, each tied to a category and carrying a
-- guided hint (not just the final answer).
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
-- Junction table: links a registered user to every problem
-- they've attempted, and whether they got it right.
CREATE TABLE IF NOT EXISTS user_progress (
  progress_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  problem_id INT NOT NULL,
  is_correct BOOLEAN DEFAULT FALSE,
  attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (problem_id) REFERENCES practice_problems(problem_id) ON DELETE CASCADE
) ENGINE=InnoDB;


-- ============================================================
-- Seed data
-- ============================================================

-- DELETE (not TRUNCATE) in child-to-parent order — this avoids MySQL's
-- "cannot truncate a table referenced in a foreign key constraint" error,
-- since deleting child rows first means nothing blocks deleting the parents.
DELETE FROM user_progress;
DELETE FROM practice_problems;
DELETE FROM formulas;
DELETE FROM categories;

-- Reset auto-increment counters so re-seeding gives the same predictable
-- IDs as a fresh run (category_id 1-6, etc.)
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
  -- Algebra
  (1, 'Quadratic Formula', 'x = (-b ± √(b²-4ac)) / 2a', 'Solves any equation of the form ax² + bx + c = 0.'),
  (1, 'Difference of Squares', 'a² - b² = (a+b)(a-b)', 'Factors a squared term minus another squared term.'),
  (1, 'Sum of Cubes', 'a³ + b³ = (a+b)(a² - ab + b²)', 'Factors the sum of two cubed terms.'),
  (1, 'Difference of Cubes', 'a³ - b³ = (a-b)(a² + ab + b²)', 'Factors the difference of two cubed terms.'),
  (1, 'Discriminant', 'Δ = b² - 4ac', 'Tells you how many real roots a quadratic has: positive means two, zero means one, negative means none.'),
  (1, 'Binomial Theorem', '(a+b)ⁿ = Σ (nCr) aⁿ⁻ʳ bʳ, r = 0 to n', 'Expands any power of a binomial without multiplying it out term by term.'),
  (1, 'nth Term of an AP', 'aₙ = a + (n-1)d', 'Finds any term of an arithmetic progression given the first term and common difference.'),
  (1, 'Sum of an AP', 'Sₙ = n/2 [2a + (n-1)d]', 'Adds up the first n terms of an arithmetic progression.'),
  (1, 'nth Term of a GP', 'aₙ = a·r^(n-1)', 'Finds any term of a geometric progression given the first term and common ratio.'),
  (1, 'Sum of a GP', 'Sₙ = a(rⁿ - 1) / (r - 1), r ≠ 1', 'Adds up the first n terms of a geometric progression.'),
  (1, 'Sum to Infinity of a GP', 'S∞ = a / (1 - r), |r| < 1', 'The limiting sum of an infinite geometric progression when the terms shrink toward zero.'),
  (1, 'Permutation', 'nPr = n! / (n-r)!', 'Counts the number of ways to arrange r items from n, where order matters.'),
  (1, 'Combination', 'nCr = n! / (r!(n-r)!)', 'Counts the number of ways to choose r items from n, where order does not matter.'),

  -- Geometry
  (2, 'Pythagorean Theorem', 'c² = a² + b²', 'Relates the sides of a right triangle.'),
  (2, 'Distance Formula', 'd = √[(x₂-x₁)² + (y₂-y₁)²]', 'Finds the straight-line distance between two points on a coordinate plane.'),
  (2, 'Midpoint Formula', 'M = ((x₁+x₂)/2, (y₁+y₂)/2)', 'Finds the point exactly halfway between two coordinates.'),
  (2, 'Slope of a Line', 'm = (y₂-y₁) / (x₂-x₁)', 'Measures the steepness and direction of a line between two points.'),
  (2, 'Slope-Intercept Form', 'y = mx + c', 'The equation of a straight line, where m is the slope and c is the y-intercept.'),
  (2, 'Equation of a Circle', '(x-h)² + (y-k)² = r²', 'Describes every point on a circle centered at (h, k) with radius r.'),
  (2, 'Section Formula', 'P = ((mx₂+nx₁)/(m+n), (my₂+ny₁)/(m+n))', 'Finds the point that divides a line segment in the ratio m:n.'),
  (2, 'Area of a Triangle (coordinates)', 'A = ½ |x₁(y₂-y₃) + x₂(y₃-y₁) + x₃(y₁-y₂)|', 'Finds the area of a triangle directly from its three coordinate points.'),
  (2, 'Area of a Circle', 'A = πr²', 'The area enclosed by a circle of radius r.'),
  (2, 'Circumference of a Circle', 'C = 2πr', 'The distance around a circle of radius r.'),

  -- Calculus
  (3, 'Power Rule (Differentiation)', 'd/dx[xⁿ] = n·xⁿ⁻¹', 'Differentiates any power of x.'),
  (3, 'Product Rule', 'd/dx[uv] = u(dv/dx) + v(du/dx)', 'Differentiates the product of two functions.'),
  (3, 'Quotient Rule', 'd/dx[u/v] = [v(du/dx) - u(dv/dx)] / v²', 'Differentiates the ratio of two functions.'),
  (3, 'Chain Rule', 'dy/dx = (dy/du) × (du/dx)', 'Differentiates a function composed inside another function.'),
  (3, 'Derivative of sin x', 'd/dx[sin x] = cos x', 'The standard derivative of the sine function.'),
  (3, 'Derivative of cos x', 'd/dx[cos x] = -sin x', 'The standard derivative of the cosine function.'),
  (3, 'Derivative of eˣ', 'd/dx[eˣ] = eˣ', 'The exponential function is its own derivative.'),
  (3, 'Derivative of ln x', 'd/dx[ln x] = 1/x', 'The standard derivative of the natural logarithm.'),
  (3, 'Power Rule (Integration)', '∫xⁿ dx = xⁿ⁺¹/(n+1) + C, n ≠ -1', 'Reverses the power rule to find the antiderivative of any power of x.'),
  (3, 'Integral of 1/x', '∫(1/x) dx = ln|x| + C', 'The antiderivative of 1/x, the one case the power rule for integration cannot handle.'),

  -- Trigonometry
  (4, 'Law of Sines', 'a/sin(A) = b/sin(B) = c/sin(C)', 'Relates side lengths to opposite angles in any triangle.'),
  (4, 'Law of Cosines', 'c² = a² + b² - 2ab·cos(C)', 'Finds a missing side or angle in any triangle, not just right triangles.'),
  (4, 'Pythagorean Identity', 'sin²θ + cos²θ = 1', 'The fundamental identity linking sine and cosine for any angle.'),
  (4, 'Tangent Identity', 'tan θ = sin θ / cos θ', 'Defines tangent in terms of sine and cosine.'),
  (4, 'Double Angle (Sine)', 'sin 2θ = 2 sin θ cos θ', 'Expands sine of a doubled angle in terms of the original angle.'),
  (4, 'Double Angle (Cosine)', 'cos 2θ = cos²θ - sin²θ', 'Expands cosine of a doubled angle in terms of the original angle.'),
  (4, 'Angle Sum (Sine)', 'sin(A+B) = sinA cosB + cosA sinB', 'Expands the sine of a sum of two angles.'),
  (4, 'Angle Sum (Cosine)', 'cos(A+B) = cosA cosB - sinA sinB', 'Expands the cosine of a sum of two angles.'),

  -- Statistics
  (5, 'Mean', 'x̄ = Σx / n', 'The average of a data set.'),
  (5, 'Median', 'Middle value when data is ordered; average of the two middle values if n is even', 'The value that splits an ordered data set exactly in half.'),
  (5, 'Variance', 'σ² = Σ(x - x̄)² / n', 'Measures how spread out a data set is from its mean.'),
  (5, 'Standard Deviation', 'σ = √(σ²)', 'The square root of variance, giving spread in the same units as the original data.'),
  (5, 'Probability of an Event', 'P(A) = (favorable outcomes) / (total outcomes)', 'The basic definition of probability for equally likely outcomes.'),
  (5, 'Conditional Probability', 'P(A|B) = P(A ∩ B) / P(B)', 'The probability of A happening, given that B has already happened.'),

  -- Linear Algebra
  (6, 'Determinant of a 2×2 Matrix', '|A| = ad - bc, for A = [[a,b],[c,d]]', 'A single number that tells you whether a 2×2 matrix can be inverted.'),
  (6, 'Inverse of a 2×2 Matrix', 'A⁻¹ = (1/|A|) [[d,-b],[-c,a]]', 'Finds the inverse of a 2×2 matrix, provided its determinant is not zero.'),
  (6, 'Matrix Multiplication (2×2)', '(AB)ᵢⱼ = Σ Aᵢₖ Bₖⱼ', 'Multiplies two matrices by taking the dot product of rows and columns.'),
  (6, 'Identity Matrix', 'I = [[1,0],[0,1]]', 'The matrix that leaves any matrix unchanged when multiplied by it — the matrix equivalent of the number 1.');

INSERT INTO practice_problems (category_id, question, difficulty, hint, answer) VALUES
  (1, 'Solve: 2x² + 5x - 3 = 0', 'Medium', 'Try factoring by grouping. Look for two numbers that multiply to (2 x -3) and add to 5.', 'x = 1/2 or x = -3'),
  (1, 'Factor: x² - 9', 'Easy', 'This is a difference of squares. Think (a+b)(a-b).', '(x+3)(x-3)'),
  (2, 'A right triangle has legs 3 and 4. Find the hypotenuse.', 'Easy', 'Use the Pythagorean theorem: c squared = a squared + b squared.', '5'),
  (3, 'Find d/dx of x³ + 2x', 'Medium', 'Apply the power rule to each term separately.', '3x² + 2');

-- Optional: link a practice attempt to one of your real registered
-- accounts for the demo. Replace the email below with one you actually
-- signed up with — this uses a subquery so you never need to know
-- the numeric user id.
-- INSERT INTO user_progress (user_id, problem_id, is_correct)
-- SELECT id, 1, TRUE FROM users WHERE email = 'your_test_account@email.com' LIMIT 1;


-- ============================================================
-- Demo JOIN queries — run these live in the phpMyAdmin SQL tab
-- during the midterm walkthrough (Activity 4, step 2)
-- ============================================================

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