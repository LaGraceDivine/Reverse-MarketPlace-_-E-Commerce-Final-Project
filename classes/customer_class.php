<?php
require_once "db_connection.php";

class Customer extends Database {

    /**
     * Add a new customer
     */
    public function addCustomer($full_name, $email, $password, $country, $city, $contact_number, $user_role = 2) {
        try {
            $conn = $this->connect();

            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM customers WHERE LOWER(email) = LOWER(?)");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                file_put_contents('debug.txt', "Email already exists: $email\n", FILE_APPEND);
                return "Email already exists";
            }

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert new customer
            $stmt = $conn->prepare(
                "INSERT INTO customers (full_name, email, password, country, city, contact_number, user_role)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            if ($stmt->execute([$full_name, $email, $hashedPassword, $country, $city, $contact_number, $user_role])) {
                file_put_contents('debug.txt', "Inserted: $full_name, $email, role: $user_role\n", FILE_APPEND);
                return "success";
            } else {
                $error = $stmt->errorInfo()[2];
                file_put_contents('debug.txt', "Insert failed: $error\n", FILE_APPEND);
                return "Error creating account: $error";
            }

        } catch (Exception $e) {
            file_put_contents('debug.txt', "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            return "Exception: " . $e->getMessage();
        }
    }

    /**
     * Get customer by email
     */
    public function getCustomerByEmail($email) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT * FROM customers WHERE LOWER(email) = LOWER(?)");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            file_put_contents('debug.txt', "getCustomerByEmail Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            return null;
        }
    }

    /**
     * Get customer by ID
     */
    public function getCustomerById($id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            file_put_contents('debug.txt', "getCustomerById Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            return null;
        }
    }

    /**
     * Login customer - validates email and password
     */
    public function loginCustomer($email, $password) {
        try {
            $customer = $this->getCustomerByEmail($email);

            if ($customer && password_verify($password, $customer['password'])) {
                return $customer;
            }
            return false;
        } catch (Exception $e) {
            file_put_contents('debug.txt', "loginCustomer Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }

    /**
     * Update customer information
     */
    public function updateCustomer($id, $full_name, $email, $country, $city, $contact_number) {
        try {
            $conn = $this->connect();
            
            $stmt = $conn->prepare(
                "UPDATE customers 
                 SET full_name = ?, email = ?, country = ?, city = ?, contact_number = ?
                 WHERE id = ?"
            );

            if ($stmt->execute([$full_name, $email, $country, $city, $contact_number, $id])) {
                return "success";
            } else {
                $error = $stmt->errorInfo()[2];
                return "Error updating customer: $error";
            }

        } catch (Exception $e) {
            file_put_contents('debug.txt', "updateCustomer Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            return "Exception: " . $e->getMessage();
        }
    }

    /**
     * Delete customer
     */
    public function deleteCustomer($id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
            
            if ($stmt->execute([$id])) {
                return "success";
            } else {
                $error = $stmt->errorInfo()[2];
                return "Error deleting customer: $error";
            }

        } catch (Exception $e) {
            file_put_contents('debug.txt', "deleteCustomer Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            return "Exception: " . $e->getMessage();
        }
    }

    /**
     * Get all customers
     */
    public function getAllCustomers() {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT * FROM customers ORDER BY id DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            file_put_contents('debug.txt', "getAllCustomers Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            return [];
        }
    }
}
?>