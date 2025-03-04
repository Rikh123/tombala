# 🎟️ **Tambola Ticket Management System** 🎟️

A web-based system to manage **Tambola tickets** with features like:
- **Efficient loading** of tickets using pagination and range-based fetch.  
- **Sorting** by ticket number (ascending/descending).  
- **Responsive grid layout** for a seamless user experience.  

---

## 📋 **Features**
1. **Load Tickets Efficiently:**  
   - Loads 10 tickets by default to ensure smooth performance.  
   - Supports **"Load More"** functionality for additional tickets.  

2. **Range-Based Fetching:**  
   - Fetch specific tickets using input (e.g., `1-50` or `1`).  

3. **Sorting:**  
   - Sort tickets by number in ascending or descending order.  

4. **Responsive Design:**  
   - Uses CSS Grid for a clean and adaptable ticket layout.  

---

## 🛠️ **Technologies Used**
- **Frontend:** HTML, CSS, JavaScript (Axios for API requests)  
- **Backend:** PHP, MySQL  
- **Database:** `tambola_game` with a `tickets` table.  

---

## 🗄️ **Database Schema (`tickets` Table)**
```sql
CREATE TABLE tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    game_id INT NOT NULL,
    player_name VARCHAR(100),
    ticket TEXT NOT NULL
);
```

---

## 🚀 **Getting Started**
### 1. **Clone the Repository**
```bash
git clone https://github.com/your-username/tambola-ticket-management.git
cd tambola-ticket-management
```

---

### 2. **Setup Database**
- Create a MySQL database named **`tambola_game`**.  
- Import the provided **`database.sql`** file to create the `tickets` table.  

---

### 3. **Configure Backend (`getTickets.php`)**
- Ensure the **database credentials** in `getTickets.php` match your MySQL setup:
   ```php
   $host = 'localhost';
   $user = 'root';
   $pass = '';
   $db = 'tambola_game';
   ```

---

### 4. **Run the Project**
- Serve the project using a local server (like **XAMPP** or **WAMP**).  
- Access the site via:  
   ```
   http://localhost/tambola-ticket-management
   ```

---

## 📄 **API Endpoints**
### 1. **Fetch Tickets**
**URL:** `/getTickets.php`  
**Method:** `GET`  
**Parameters:**
- `game_id` (required): ID of the game.  
- `limit` (optional): Number of tickets to load (default: `10`).  
- `offset` (optional): Offset for pagination (default: `0`).  
- `range` (optional): Specific range or single ticket ID (e.g., `1-50` or `1`).  

**Example:**
```bash
/getTickets.php?game_id=1&limit=10&offset=0
/getTickets.php?game_id=1&range=1-50
/getTickets.php?game_id=1&range=5
```

---

### 🔄 **API Response:**
**Success:**
```json
[
    {
        "id": "11",
        "player_name": "",
        "ticket": "[[0,16,28,37,0,0,65,0,91],[7,0,0,0,46,59,64,0,93],[0,13,0,0,45,54,70,0,92]]"
    },
    ...
]
```

**Error:**
```json
{
    "error": "Invalid game ID."
}
```

---

## 🖼️ **Frontend Structure**
### Key Files:
- **`index.html`**: Main UI with modal for tickets.  
- **`style.css`**: Responsive grid layout and ticket styling.  
- **`app.js`**: Handles API requests and dynamic rendering.  

---

### 🔄 **JavaScript Functions (`app.js`):**
1. **`openTicketModal(gameId)`**:  
   - Fetches and displays tickets based on the game ID.  
   - Supports **pagination, range, and sorting**.  

2. **`saveTickets(tickets)`**:  
   - Saves ticket data (To be implemented).  

---

## 🎨 **Styling (`style.css`):**
- **Grid Layout:**
   ```css
   #tickets {
       display: grid;
       grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
       gap: 20px;
       padding: 20px;
   }
   ```

- **Ticket Table:**
   ```css
   .ticket-container table {
       width: 100%;
       border-collapse: collapse;
       box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
   }
   .ticket-container td.filled {
       background-color: #f0f0f0;
   }
   ```

---

## 🛠️ **Future Enhancements**
- **Search by Player Name.**  
- **Export tickets** to PDF or Excel.  
- **Authentication** for admins.  

---

## 📞 **Support**
For issues, open an issue on GitHub or contact me at **kangleiinivations@gmail.com**.

---

### ⭐ **Enjoy managing your Tambola tickets!** ⭐