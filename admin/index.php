<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel - Tambola Game</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<style>
    #tickets {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
}

.ticket-container {
    text-align: center;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    padding: 10px;
}

.ticket-container h4 {
    margin-bottom: 10px;
    font-size: 18px;
    color: #333;
}

.ticket-container table {
    width: 100%;
    border-collapse: collapse;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    margin-bottom: 10px;
}

.ticket-container td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
}

.ticket-container td.filled {
    background-color: #f0f0f0;
    font-weight: bold;
}

.ticket-container table,
.ticket-container td {
    max-width: 100%;
    word-wrap: break-word;
}

.countdown {
    font-size: 20px;
    margin: 10px 0;
}

</style>
</head>

<body class="bg-gray-100">

<!-- Header -->
<header class="bg-blue-600 text-white p-4 text-center text-2xl font-semibold shadow-md">
    Admin Panel - Tambola Game Online 🎲 
</header>

<div class="container mx-auto p-4">
    <!-- Schedule Game -->
    <section class="bg-white p-6 rounded-lg shadow mb-6">
        <h2 class="text-xl font-bold mb-4">Schedule New Game 🗓️</h2>
        <form id="scheduleForm" class="flex items-center gap-2">
            <input type="datetime-local" id="gameTime" class="border p-2 rounded w-full" required>
            <input type="number" id="ticketCount" class="border p-2 rounded w-full" placeholder="Number of Tickets" min="1" required>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                Schedule Game
            </button>
        </form>
    </section>

    <!-- Scheduled Games -->
    <section class="bg-white p-6 rounded-lg shadow mb-6">
        <h2 class="text-xl font-bold mb-4">Scheduled Games 🕒</h2>
        <table class="w-full border-collapse text-center">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Game ID</th>
                    <th class="border p-2">Scheduled Time</th>
                    <th class="border p-2">Ticket Count</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody id="gamesList">
                <!-- Dynamic Game Rows -->
            </tbody>
        </table>
    </section>
    <!-- Ticket Booking Modal -->
<div id="ticketModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-4 rounded w-1/2">
        <h2 class="text-lg font-bold mb-2">Book Tickets</h2>
        <div id="ticketModalContent" class="max-h-96 overflow-y-auto"></div>
        <div class="mt-4 flex justify-end gap-2">
            <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                    onclick="closeModal()">Close</button>
            <button id="saveTicketsBtn" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Save</button>
        </div>
    </div>
</div>

</div>


<script>
// Example Backend Endpoints (Adjust as needed)
const API = {
    getGames: 'get_scheduled_games.php',
    scheduleGame: 'schedule_game.php',
    updateGame: 'update_game.php',
    startGame: 'start_game.php',
    generateTickets: 'generate_tickets.php',
    getTickets: 'get_tickets.php',
    updateTickets: 'updateTickets.php',
};

// Fetch Scheduled Games
async function fetchScheduledGames() {
    try {
        const res = await axios.get(API.getGames);
        const games = res.data || [];
        const gamesList = document.getElementById('gamesList');
        gamesList.innerHTML = '';

        games.forEach(game => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="border p-2">${game.id}</td>
                <td class="border p-2">
                    <input type="datetime-local" value="${game.scheduled_time}" class="border p-1 rounded w-full" disabled
                           id="dateTime-${game.id}">
                </td>
                <td class="border p-2">
                    <input type="number" value="${game.ticket_count}" min="1" class="border p-1 rounded w-full" disabled
                           id="ticketCount-${game.id}">
                </td>
                <td class="border p-2">${game.status}</td>
                <td class="border p-2 flex justify-center gap-2">
                    <button class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600"
                            onclick="enableEdit(${game.id})" id="editBtn-${game.id}">Edit</button>
                    <button class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 hidden"
                            onclick="updateGame(${game.id})" id="updateBtn-${game.id}">Update</button>
                    <button class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600"
                            onclick="startGame(${game.id})">Start</button>
                    <button class="bg-purple-500 text-white px-2 py-1 rounded hover:bg-purple-600"
                            onclick="openTicketModal(${game.id})">Book Ticket</button>
                </td>
            `;
            gamesList.appendChild(row);
        });
    } catch (error) {
        console.error('Error fetching games:', error);
        alert('Failed to fetch scheduled games.');
    }
}

let currentOffset = 0;
const limit = 50;  // Load 10 tickets per batch

async function openTicketModal(gameId) {
    try {
        const res = await axios.get(`${API.getTickets}?game_id=${gameId}&limit=${limit}&offset=${currentOffset}`);
        let tickets = res.data || [];
        const modalContent = document.getElementById('ticketModalContent');
        modalContent.innerHTML = '';  // Clear previous tickets

        // Dropdown for sorting and range input
        modalContent.innerHTML += `
            <div class="mb-4 text-center">
                <label for="sortTickets" class="font-bold">Sort by Ticket No:</label>
                <select id="sortTickets" class="border p-1 rounded ml-2">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
                <input type="text" id="ticketRange" placeholder="e.g. 1-50 or 1" class="border p-1 rounded ml-2" />
                <button id="loadRangeBtn" class="border p-1 rounded bg-blue-500 text-white ml-2">Load Range</button>
            </div>
        `;

        const renderTickets = (tickets) => {
            modalContent.innerHTML += `<div id="tickets">`;
            tickets.forEach(ticket => {
                const parsedTicket = JSON.parse(ticket.ticket);
                modalContent.innerHTML += `
                    <div class="ticket-container">
                        <h4>Ticket No: ${ticket.id}</h4>
                        <input type="text" value="${ticket.player_name || ''}" placeholder="Enter Player Name" 
                               class="border p-1 rounded w-full mb-2"
                               id="playerName-${ticket.id}">
                        <table>
                            ${parsedTicket.map(row => `
                                <tr>
                                    ${row.map(cell => `
                                        <td class="${cell === 0 ? '' : 'filled'}">${cell === 0 ? '' : cell}</td>
                                    `).join('')}
                                </tr>
                            `).join('')}
                        </table>
                    </div>
                `;
            });
            modalContent.innerHTML += `</div>`;
        };

        renderTickets(tickets);

        // Load more tickets
        document.getElementById('loadMoreBtn')?.remove();
        modalContent.innerHTML += `
            <button id="loadMoreBtn" class="border p-1 rounded bg-green-500 text-white mt-4">Load More</button>
        `;

        document.getElementById('loadMoreBtn').addEventListener('click', async () => {
            currentOffset += limit;
            const res = await axios.get(`${API.getTickets}?game_id=${gameId}&limit=${limit}&offset=${currentOffset}`);
            const moreTickets = res.data || [];
            renderTickets(moreTickets);
        });

        // Load tickets by range
        document.getElementById('loadRangeBtn').addEventListener('click', async () => {
            const range = document.getElementById('ticketRange').value.trim();
            if (range) {
                const res = await axios.get(`${API.getTickets}?game_id=${gameId}&range=${range}`);
                tickets = res.data || [];
                modalContent.innerHTML = modalContent.innerHTML.split('</div>')[0] + '</div>';  // Clear ticket grid
                renderTickets(tickets);
            }
        });

        // Sorting event listener
        document.getElementById('sortTickets').addEventListener('change', (e) => {
            const sortOrder = e.target.value;
            tickets = tickets.sort((a, b) => {
                return sortOrder === 'asc' ? a.id - b.id : b.id - a.id;
            });
            modalContent.innerHTML = modalContent.innerHTML.split('</div>')[0] + '</div>';  // Clear ticket grid
            renderTickets(tickets);
        });

        // Show modal
        document.getElementById('ticketModal').classList.remove('hidden');
        document.getElementById('saveTicketsBtn').onclick = () => saveTickets(tickets);
    } catch (error) {
        console.error('Error fetching tickets:', error);
        alert('Failed to fetch tickets.');
    }
}




// Save Updated Tickets
async function saveTickets(tickets) {
    const updatedTickets = tickets.map(ticket => ({
        id: ticket.id,
        player_name: document.getElementById(`playerName-${ticket.id}`).value.trim() || ''
    }));

    try {
        const res = await axios.post(API.updateTickets, { tickets: updatedTickets });
        alert(res.data.message || 'Tickets updated successfully!');
        document.getElementById('ticketModal').classList.add('hidden');  // Hide modal
    } catch (error) {
        console.error('Error updating tickets:', error);
        alert('Failed to update tickets.');
    }
}

// Format ticket for display
function formatTicket(ticket) {
    return ticket.map(row => row.join('  ')).join('\n');
}

// Close Modal
function closeModal() {
    document.getElementById('ticketModal').classList.add('hidden');
}


// Enable Edit Mode
function enableEdit(gameId) {
    // Enable inputs for editing
    document.getElementById(`dateTime-${gameId}`).disabled = false;
    document.getElementById(`ticketCount-${gameId}`).disabled = false;

    // Show the Update button and hide the Edit button
    document.getElementById(`editBtn-${gameId}`).classList.add('hidden');
    document.getElementById(`updateBtn-${gameId}`).classList.remove('hidden');
}

// Update Game Details
async function updateGame(gameId) {
    const newTime = document.getElementById(`dateTime-${gameId}`).value;
    const newTicketCount = document.getElementById(`ticketCount-${gameId}`).value;

    if (!newTime || newTicketCount <= 50) {
        alert('Please enter a valid date, time, and ticket count not less than 50');
        return;
    }

    try {
        const formData = new URLSearchParams();
        formData.append('game_id', gameId);
        formData.append('scheduled_time', newTime);
        formData.append('ticket_count', newTicketCount);

        const res = await axios.post(API.updateGame, formData);
        alert(res.data.message || 'Game details updated!');

        // Disable inputs and switch buttons back
        document.getElementById(`dateTime-${gameId}`).disabled = true;
        document.getElementById(`ticketCount-${gameId}`).disabled = true;
        document.getElementById(`editBtn-${gameId}`).classList.remove('hidden');
        document.getElementById(`updateBtn-${gameId}`).classList.add('hidden');

        fetchScheduledGames(); // Refresh list
    } catch (error) {
        console.error('Error updating game:', error);
        alert('Failed to update game details.');
    }
}


// Schedule Game
document.getElementById('scheduleForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const gameTime = document.getElementById('gameTime').value;
    const ticketCount = document.getElementById('ticketCount').value;
    if (!gameTime || !ticketCount) return alert('Please select time and enter the number of tickets!');

    try {
        await axios.post(API.scheduleGame, new URLSearchParams({ scheduled_time: gameTime, ticket_count: ticketCount }));
        fetchScheduledGames();
        alert('Game scheduled successfully!');
    } catch (error) {
        console.error('Error scheduling game:', error);
        alert('Failed to schedule game.');
    }
});


// Initial Fetch
fetchScheduledGames();
</script>

</body>
</html>
