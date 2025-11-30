-- Fix Foreign Key Constraints for Disputes Table
-- Run this script if you already created the disputes table with incorrect foreign keys

-- First, check existing constraints
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'disputes'
AND CONSTRAINT_SCHEMA = DATABASE()
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Drop existing foreign key constraints (use the actual constraint names from above)
-- Common default names are disputes_ibfk_1 and disputes_ibfk_2
-- Adjust these names based on what you see in the query above

ALTER TABLE disputes DROP FOREIGN KEY disputes_ibfk_1;
ALTER TABLE disputes DROP FOREIGN KEY disputes_ibfk_2;

-- Add correct foreign key constraints
ALTER TABLE disputes 
ADD CONSTRAINT fk_disputes_user 
FOREIGN KEY (user_id) REFERENCES customers(id) ON DELETE CASCADE;

ALTER TABLE disputes 
ADD CONSTRAINT fk_disputes_order 
FOREIGN KEY (order_id) REFERENCES orders(orderid) ON DELETE SET NULL;

-- Verify the new constraints
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'disputes'
AND CONSTRAINT_SCHEMA = DATABASE()
AND REFERENCED_TABLE_NAME IS NOT NULL;
