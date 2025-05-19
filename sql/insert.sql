
-- Postres y Tartas
INSERT INTO productos (nombre, descripcion, categoria_principal, subcategoria, tiene_tamanios, precio_mediano, raciones_mediano, precio_grande, raciones_grande, precio_unitario, imagen, disponible, destacado)
VALUES 
('Tarta de tres leches', 'Delicioso bizcocho esponjoso empapado en una mezcla de leche evaporada, condensada y crema, coronado con merengue', 'POSTRES', 'tartas', TRUE, 35.00, '8-12 porciones', 45.00, '16-20 porciones', NULL, 'img/postres/3_leches.jpg', TRUE, TRUE),
('Crema volteada', 'Clásico postre de huevo y leche condensada con un caramelo dorado que se convierte en salsa al desmoldar', 'POSTRES', 'postres', TRUE, 32.00, '8-12 porciones', 40.00, '16-20 porciones', NULL, 'img/postres/crema_volteada.jpg', TRUE, FALSE),
('Pay de limón', 'Base crujiente de galleta rellena de cremosa mezcla de limón con un toque de ralladura cítrica', 'POSTRES', 'postres', TRUE, 25.00, '8-10 porciones', 35.00, '12-16 porciones', NULL, 'img/postres/pay_limon.jpg', TRUE, FALSE),
('Pay de manzana', 'Fina capa de hojaldre con relleno de manzana caramelizada y canela, horneado hasta dorar', 'POSTRES', 'postres', TRUE, 25.00, '8-10 porciones', 35.00, '12-16 porciones', NULL, 'img/postres/pay_manzana.jpg', TRUE, TRUE),
('Tarta de chocolate', 'Bizcocho de chocolate negro con  decoración de ganache  de chocolate', 'POSTRES', 'tartas', TRUE, 40.00, '8-12 porciones', 55.00, '16-20 porciones', NULL, 'img/postres/tarta_chocolate.jpg', TRUE, TRUE),
('Tarta de zanahoria', 'Húmedo bizcocho de zanahoria con nueces, especias y cubierto con frosting de queso crema', 'POSTRES', 'tartas', TRUE, 38.00, '8-12 porciones', 45.00, '16-20 porciones', NULL, 'img/postres/tarta_zanahoria.jpg', TRUE, FALSE),
('Tarta Helada', 'Fresco postre con base de galleta, mousse de fresa y trozos de durazno en gelatina natural','POSTRES', 'tartas', TRUE, 35.00, '8-12 porciones', NULL, NULL, NULL,'img/postres/tarta_helada.jpg', TRUE, TRUE),
('Pionono', 'Bizcocho suave enrollado con dulce de leche', 'POSTRES', 'postres', TRUE, 25.00, '8 porciones', NULL, NULL, NULL, 'img/postres/tarta_helada.jpg', TRUE, FALSE),
('Strawberry', 'Bizcocho suave enrollado de vainilla suave decorado con nata y fresas', 'POSTRES', 'postres', TRUE, NULL, NULL, 40.00, '16-20 porciones', NULL, 'img/postres/strawberry_cake.jpg', TRUE, FALSE);


-- Bocaditos  salados
INSERT INTO productos (
  nombre, descripcion, categoria_principal, subcategoria,
  tiene_tamanios, precio_mediano, raciones_mediano,
  precio_grande, raciones_grande, precio_unitario,
  imagen, disponible, destacado
)
VALUES
-- 1
('Mini Croissant ', 'Mini croissant con aguacate y salmón', 'BOCADITOS', 'salados',
 FALSE, NULL, NULL, NULL, NULL, 2.00,
 'img/postres/mini_aguacate.jpg', TRUE, TRUE),

-- 2
('Mini Empanadas', 'Mini empanadas con relleno de champiñones', 'BOCADITOS', 'salados',
 FALSE, NULL, NULL, NULL, NULL, 1.50,
 'img/postres/mini_empanadas_champi.jpg', TRUE, FALSE),

-- 3
('Mini Empanadas ', 'Mini empanadas rellenas de carne', 'BOCADITOS', 'salados',
 FALSE, NULL, NULL, NULL, NULL, 1.50,
 'img/postres/mini_empanadas_carne.jpg', TRUE, TRUE),

-- 4
('Mini Empanadas', 'Mini empanadas rellenas de pollo', 'BOCADITOS', 'salados',
 FALSE, NULL, NULL, NULL, NULL, 1.50,
 'img/postres/mini_empanadas_pollo.jpg', TRUE, FALSE),

-- 5
('Mini Enrollado', 'Mini enrollado con durazno y jamón', 'BOCADITOS', 'salados',
 FALSE, NULL, NULL, NULL, NULL, 1.50,
 'img/postres/mini_enrollado_durazno.jpg', TRUE, FALSE),

-- 6
('Mini Mixto', 'Mini croissant con jamón y queso', 'BOCADITOS', 'salados',
 FALSE, NULL, NULL, NULL, NULL, 1.50,
 'img/postres/mini_mixto.jpg', TRUE, FALSE);


 -- Bocaditos dulces

 INSERT INTO productos (
  nombre, descripcion, categoria_principal, subcategoria,
  tiene_tamanios, precio_mediano, raciones_mediano,
  precio_grande, raciones_grande, precio_unitario,
  imagen, disponible, destacado
)
VALUES
-- 1
('Mini Alfajorcito', 'Galletitas rellenas con dulce de leche', 'BOCADITOS', 'dulces',
 FALSE, NULL, NULL, NULL, NULL, 1.00,
 'img/postres/dulce_alfajorcito.jpg', TRUE, TRUE),

-- 2
('Mini Brownies', 'Deliciosos mini brownies de chocolate intenso', 'BOCADITOS', 'dulces',
 FALSE, NULL, NULL, NULL, NULL, 1.00,
 'img/postres/dulce_brownicitos.jpg', TRUE, TRUE),

-- 3
('Mini Carrot Cake', 'Pastelito de zanahoria con suave cobertura', 'BOCADITOS', 'dulces',
 FALSE, NULL, NULL, NULL, NULL, 1.00,
 'img/postres/dulce_carrotcitos.jpg', TRUE, FALSE),

-- 4
('Mini Cheesecake de Fresa', 'Base de galleta y queso horneado con fresa', 'BOCADITOS', 'dulces',
 FALSE, NULL, NULL, NULL, NULL, 2.00,
 'img/postres/dulce_cheskeicito_fresa.jpg', TRUE, FALSE),

-- 5
('Mini Milhojas', 'Capas de hojaldre con crema pastelera', 'BOCADITOS', 'dulces',
 FALSE, NULL, NULL, NULL, NULL, 1.00,
 'img/postres/dulce_milhojitas.jpg', TRUE, FALSE),

-- 6
('Mini Pay de Limón', 'Base crujiente con crema suave de limón', 'BOCADITOS', 'dulces',
 FALSE, NULL, NULL, NULL, NULL, 1.00,
 'img/postres/dulce_paycito_limon.jpg', TRUE, FALSE);



