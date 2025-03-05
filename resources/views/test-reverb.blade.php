<!-- resources/views/test-reverb.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Reverb Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app/plugin.js'])
</head>
<body>
<div id="app">
    <h1>Reverb Test for Ticket #{{ $ticketId }}</h1>

    <div style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: auto; margin-bottom: 20px;" id="messages">
        <!-- Messages will appear here -->
    </div>

    <div>
        <input type="text" id="message" placeholder="Enter a test message" style="padding: 8px; width: 300px;">
        <button id="send" style="padding: 8px 16px; background: #4A90E2; color: white; border: none; cursor: pointer;">Send Test Event</button>
    </div>
</div>

<script>
    // Wait for Echo to be initialized
    document.addEventListener('DOMContentLoaded', function() {
        // Make sure CSRF token is set
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Subscribe to the private channel
        window.Echo.private('ticket.{{ $ticketId }}')
            .listen('Padmission\\Tickets\\Events\\TicketCommentCreated', (event) => {
                console.log('Received event:', event);

                // Add message to the messages div
                const messagesDiv = document.getElementById('messages');
                const messageDiv = document.createElement('div');
                messageDiv.style.padding = '8px';
                messageDiv.style.margin = '4px 0';
                messageDiv.style.background = '#f0f0f0';
                messageDiv.textContent = `Message: ${event.message}`;

                messagesDiv.appendChild(messageDiv);
            });

        // Send button handler
        document.getElementById('send').addEventListener('click', function() {
            const message = document.getElementById('message').value;

            if (!message) return;

            // Send via ajax to trigger the event
            axios.post('/broadcast-test', {
                message: message,
                ticketId: {{ $ticketId }}
            })
                .then(response => {
                    console.log('Event sent:', response.data);
                    document.getElementById('message').value = '';
                })
                .catch(error => {
                    console.error('Error sending event:', error);
                });
        });
    });
</script>
</body>
</html>
