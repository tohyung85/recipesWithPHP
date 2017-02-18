CREATE TABLE `recipes` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(60)
);

CREATE TABLE `recipesteps` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `step` TEXT,
  `photo` VARCHAR(100),
  `recipe_id` INT NOT NULL,
  FOREIGN KEY (`recipe_id`) REFERENCES recipes (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  `step_order` INT NOT NULL
);

INSERT INTO `recipes` VALUES(0, 'Fried Chicken');
INSERT INTO `recipesteps` VALUES(0, '1 Chicken', '', 1, 1);
INSERT INTO `recipesteps` VALUES(0, '2 tablespoons of Oil', '', 1, 2);
INSERT INTO `recipesteps` VALUES(0, 'Add 300g of Flour', '', 1, 3);
INSERT INTO `recipesteps` VALUES(0, 'Fry it', '', 1, 4);