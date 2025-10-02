<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Catatan Pribadi</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menggunakan font Inter */
        body { font-family: 'Inter', sans-serif; }
        .calendar-grid {
            grid-template-columns: repeat(7, 1fr);
        }
        /* Style untuk tanggal yang memiliki notes */
        .has-notes {
            position: relative;
        }
        .note-indicator {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            pointer-events: none;
        }
        /* Indikator Urgensi */
        .urgency-high { background-color: #ef4444; }
        .urgency-medium { background-color: #f59e0b; }
        .urgency-low { background-color: #10b981; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">

    <div class="p-4 sm:p-6 lg:p-8 min-h-screen">
        
        <!-- Informasi User & Status -->
        <div class="mb-4 flex flex-col sm:flex-row sm:justify-between sm:items-center text-sm space-y-2 sm:space-y-0">
            <div class="flex flex-col space-y-1">
                <span id="status-message" class="font-semibold text-indigo-600 dark:text-indigo-400">Memuat status...</span>
                <span id="user-id-display" class="text-xs text-gray-500 dark:text-gray-400 break-all">ID Pengguna: Belum Terautentikasi</span>
            </div>
            
            <!-- Tombol Non-Destruktif untuk Menguji Sesi Baru -->
            <button id="new-session-btn" onclick="startNewSessionTest()" class="px-3 py-1 bg-red-500 text-white dark:bg-red-600 dark:hover:bg-red-700 rounded-lg text-xs font-medium hover:bg-red-600 transition duration-150">
                Uji Sesi Baru (Ganti User ID)
            </button>
        </div>

        <!-- Kalender Utama -->
        <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-3">Kalender Catatan Pribadi</h2>
            
            <!-- Header Navigasi Kalender -->
            <div class="flex justify-between items-center mb-6">
                <button id="prev-month" class="p-2 rounded-full text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-gray-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h3 id="month-year" class="text-xl font-semibold text-gray-900 dark:text-white"></h3>
                <button id="next-month" class="p-2 rounded-full text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-gray-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Nama Hari -->
            <div class="calendar-grid grid text-sm font-medium text-gray-500 dark:text-gray-400 border-b border-t py-2 mb-2">
                <div class="text-center">Sen</div>
                <div class="text-center">Sel</div>
                <div class="text-center">Rab</div>
                <div class="text-center">Kam</div>
                <div class="text-center">Jum</div>
                <div class="text-center">Sab</div>
                <div class="text-center text-red-500">Min</div>
            </div>
            
            <!-- Hari Kalender (diisi oleh JS) -->
            <div id="calendar-days" class="calendar-grid grid gap-1 sm:gap-2">
                <!-- Days will be inserted here by JavaScript -->
            </div>
        </div>

    </div>

    <!-- Modal untuk Notes / To-Do List -->
    <div id="note-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md p-6 rounded-xl shadow-2xl">
            
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Catatan untuk</h3>
                <button id="close-modal-btn" class="text-gray-400 hover:text-gray-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <p id="note-date-display" class="text-lg font-semibold text-indigo-600 mb-4"></p>

            <!-- Tampilan Notes yang Sudah Ada -->
            <div id="note-list" class="mb-4">
                <!-- Notes will be displayed here -->
            </div>

            <!-- Form Tambah/Edit Notes -->
            <form id="note-form" class="space-y-4">
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes / Keterangan Event</label>
                    <textarea id="notes" name="notes" rows="3" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white p-2"
                        placeholder="Masukkan detail notes atau event..."></textarea>
                </div>
                
                <div>
                    <label for="urgency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tingkat Urgensi</label>
                    <select id="urgency" name="urgency" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white p-2">
                        <option value="low">Rendah (Hijau)</option>
                        <option value="medium">Sedang (Jingga)</option>
                        <option value="high">Tinggi (Merah)</option>
                    </select>
                </div>

                <div class="flex justify-end pt-4 border-t mt-4">
                    <button type="submit" id="save-button"
                        class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition duration-150 shadow-lg shadow-indigo-500/50">
                        Simpan Notes
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Konfirmasi Hapus -->
    <div id="confirmation-dialog" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-sm p-6 rounded-xl shadow-2xl">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Konfirmasi Hapus</h3>
            <p id="confirm-message" class="text-gray-700 dark:text-gray-300 mb-6">Anda yakin ingin menghapus notes ini?</p>
            <div class="flex justify-end space-x-3">
                <button type="button" id="confirm-no" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Batal</button>
                <button type="button" id="confirm-yes" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <!-- Firebase SDKs & JavaScript Utama -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getAuth, signInAnonymously, onAuthStateChanged, signOut, setPersistence, browserSessionPersistence } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        import { getFirestore, collection, doc, setDoc, onSnapshot } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        import { setLogLevel } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";

        // ====================================================================
        // GANTI DENGAN KONFIGURASI FIREBASE ANDA YANG ASLI
        // ====================================================================
        const FIREBASE_CONFIG_JSON = {
            "apiKey": "AIzaSyDMvMaPDHOIVH7mJCZTb-dPgj4daEXBuqU", 
            "authDomain": "pklpalawi.firebaseapp.com",
            "projectId": "pklpalawi",
            "storageBucket": "pklpalawi.appspot.com",
            "messagingSenderId": "217062069271",
            "appId": "1:217062069271:web:c546f8ad2456e4a3c33852",
            "measurementId": "G-3TLG8703DX"
        };
        // ====================================================================
        
        const APP_ID = FIREBASE_CONFIG_JSON.projectId || 'default-app-id'; 

        let db, auth;
        let unsubscribeNotes = null; 
        
        if (!FIREBASE_CONFIG_JSON || !FIREBASE_CONFIG_JSON.apiKey) {
            console.error("Firebase configuration is missing or invalid. Check FIREBASE_CONFIG_JSON.");
            document.getElementById('status-message').textContent = "ERROR: Firebase tidak terinisialisasi. Periksa konfigurasi.";
        } else {
            // 1. Initialize Firebase
            const app = initializeApp(FIREBASE_CONFIG_JSON);
            db = getFirestore(app);
            auth = getAuth(app);
            setLogLevel('Debug'); 
        }

        let currentUserId = null;
        let notesData = {}; 

        const calendarElement = document.getElementById('calendar-days');
        const monthYearElement = document.getElementById('month-year');
        const modalElement = document.getElementById('note-modal');
        const noteForm = document.getElementById('note-form');
        const noteDateDisplay = document.getElementById('note-date-display');
        const noteListElement = document.getElementById('note-list');
        const userIdDisplay = document.getElementById('user-id-display');

        let currentDate = new Date();
        let selectedDate = null;
        
        function getCollectionPath() {
            if (!currentUserId) return null;
            return `artifacts/${APP_ID}/users/${currentUserId}/personal_notes`;
        }
        
        // FUNGSI UNTUK UJI SESI BARU (Non-Destruktif)
        window.startNewSessionTest = async function() {
            if (!auth) return;
            
            document.getElementById('status-message').textContent = "Status: Mencoba memulai sesi baru (Sign Out)...";
            userIdDisplay.textContent = "ID Pengguna: Proses ganti sesi...";
            
            try {
                // Logout dari sesi saat ini
                await signOut(auth);
                
                // PENTING: Karena signOut, onAuthStateChanged akan mendeteksi user=null 
                // dan memicu signInAnonymously baru di langkah berikutnya.
                document.getElementById('status-message').textContent = "Sign Out berhasil. Mencoba login anonim baru...";

            } catch (error) {
                console.error("Error signing out:", error);
                document.getElementById('status-message').textContent = `ERROR: Gagal Sign Out (${error.code}).`;
            }
        };


        // 2. Authentication and Initialization
        async function initializeAuth() {
            if (!auth) return; 
            
            try {
                // PENTING: Mengatur Persistence agar sesi anonim TIDAK HILANG saat refresh
                // Kita gunakan LOCAL, yang bertahan bahkan setelah browser ditutup, 
                // agar data User A bisa kembali saat user A "login" lagi
                await setPersistence(auth, browserSessionPersistence);

                if (!auth.currentUser) {
                    await signInAnonymously(auth);
                }
            } catch (error) {
                console.error("Firebase Auth Error:", error);
                let message = `ERROR AUTENTIKASI: ${error.code}. Mohon cek Firebase Console.`;
                document.getElementById('status-message').textContent = message;
            }
        }
        
        if (auth) { 
            onAuthStateChanged(auth, async (user) => {
                // 1. CLEAN UP (Hentikan listener notes lama)
                if (unsubscribeNotes) {
                    unsubscribeNotes(); 
                    unsubscribeNotes = null;
                    console.log("[AUTH] Listener Firestore lama dihentikan.");
                }
                
                notesData = {}; 
                document.getElementById('calendar-days').innerHTML = ''; 
                
                if (user) {
                    // 2. USER IS LOGGED IN
                    currentUserId = user.uid;
                    document.getElementById('status-message').textContent = `Status: Terhubung`; 
                    userIdDisplay.textContent = `ID Pengguna: ${currentUserId}`; 
                    console.log(`[AUTH] User ID aktif: ${currentUserId}`);
                    
                    // 3. START LISTENER FOR THIS USER
                    if (db) {
                        unsubscribeNotes = setupNotesListener();
                    } else {
                        document.getElementById('status-message').textContent = "ERROR: DB tidak terinisialisasi.";
                    }

                } else {
                    // 4. NO USER (Setelah signout, atau startup pertama kali)
                    currentUserId = null;
                    userIdDisplay.textContent = "ID Pengguna: Mencoba sesi baru...";
                    document.getElementById('status-message').textContent = "Status: Mencoba memulai sesi anonim baru...";
                    console.log("[AUTH] Tidak ada user aktif. Mencoba login anonim baru...");
                    
                    try {
                        // Lakukan sign in anonim untuk mendapatkan ID baru (User ID B atau C)
                        // Karena kita menggunakan signInAnonymously tanpa menyimpan, ini akan menghasilkan ID baru
                        await signInAnonymously(auth); 
                    } catch (error) {
                        document.getElementById('status-message').textContent = `ERROR AUTENTIKASI: ${error.code}`;
                        console.error("[AUTH] Error saat sign-in:", error);
                        renderCalendar(); 
                    }
                }
            });
        }
        
        // Helper function to format date YYYY-MM-DD
        function formatDate(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        // 3. Firestore Realtime Listener
        function setupNotesListener() {
            const path = getCollectionPath();
            if (!path || !db) return;
            
            const notesRef = collection(db, path);
            
            const unsubscribe = onSnapshot(notesRef, (snapshot) => {
                notesData = {}; 
                snapshot.forEach((doc) => {
                    const data = doc.data();
                    if (data.notes) {
                         notesData[doc.id] = data;
                    }
                });
                console.log(`[FIRESTORE] Notes data updated for user ${currentUserId}: ${Object.keys(notesData).length} notes found.`);
                
                renderCalendar(); 
                
                if (modalElement.classList.contains('hidden') === false && selectedDate) {
                    displayNotesForSelectedDate(selectedDate);
                }

            }, (error) => {
                console.error("Error listening to notes. Pastikan Security Rules sudah benar!:", error);
                document.getElementById('status-message').textContent = `ERROR LISTENER: Akses Ditolak. (Cek Security Rules!).`;
            });
            
            return unsubscribe; 
        }

        // 4. Calendar Rendering
        function renderCalendar() {
            calendarElement.innerHTML = '';
            monthYearElement.textContent = currentDate.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            const firstDayOfMonth = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            const startDayIndex = (firstDayOfMonth === 0) ? 6 : firstDayOfMonth - 1; 
            for (let i = 0; i < startDayIndex; i++) {
                calendarElement.insertAdjacentHTML('beforeend', '<div class="text-center p-2"></div>');
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dateKey = formatDate(date);
                const isToday = dateKey === formatDate(new Date());
                const notes = notesData[dateKey]; 

                let dayClass = 'text-gray-900 dark:text-gray-100 hover:bg-indigo-100 dark:hover:bg-gray-700 cursor-pointer rounded-xl transition duration-150';
                if (isToday) {
                    dayClass = 'bg-indigo-500 text-white font-bold rounded-xl shadow-md';
                }

                const dayHtml = `
                    <div class="day-cell text-center p-2 pt-3 relative ${dayClass} ${notes ? 'has-notes' : ''}" 
                         data-date="${dateKey}" onclick="selectDate('${dateKey}')">
                        ${day}
                        ${notes ? `<div class="note-indicator urgency-${notes.urgency || 'low'} shadow-md"></div>` : ''}
                    </div>
                `;
                calendarElement.insertAdjacentHTML('beforeend', dayHtml);
            }
        }

        window.selectDate = selectDate;

        function changeMonth(delta) {
            currentDate.setMonth(currentDate.getMonth() + delta);
            renderCalendar();
        };

        // 5. Date Selection and Modal Handling
        function selectDate(dateKey) {
            selectedDate = dateKey;
            const dateObj = new Date(dateKey + 'T00:00:00'); 
            
            noteDateDisplay.textContent = dateObj.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            displayNotesForSelectedDate(dateKey);

            modalElement.classList.remove('hidden');
            document.getElementById('save-button').textContent = 'Simpan Notes'; 
        };
        
        function closeModal() {
            modalElement.classList.add('hidden');
            noteForm.reset();
            selectedDate = null;
            document.getElementById('save-button').textContent = 'Simpan Notes';
        };
        
        // 6. Displaying/Editing Notes
        function displayNotesForSelectedDate(dateKey) {
            noteListElement.innerHTML = '';
            const existingNote = notesData[dateKey];

            if (existingNote) {
                noteListElement.innerHTML = `
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200 dark:bg-gray-700 dark:border-gray-600">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">Status Notes:</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">${existingNote.notes}</p>
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mt-3 mb-1">Urgensi:</p>
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full text-white 
                            ${existingNote.urgency === 'high' ? 'bg-red-500' : 
                              existingNote.urgency === 'medium' ? 'bg-yellow-500' : 
                              'bg-green-500'}">
                            ${existingNote.urgency.toUpperCase()}
                        </span>
                        <div class="flex justify-end mt-4 space-x-2">
                            <button type="button" onclick="editExistingNote('${dateKey}')" 
                                class="px-3 py-1 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-lg transition duration-150">
                                Edit
                            </button>
                            <button type="button" onclick="deleteNoteConfirmation('${dateKey}')" 
                                class="px-3 py-1 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg transition duration-150">
                                Hapus
                            </button>
                        </div>
                    </div>
                `;
                noteForm.classList.add('hidden');
            } else {
                noteForm.classList.remove('hidden');
            }
        }
        
        window.editExistingNote = editExistingNote;
        window.deleteNoteConfirmation = deleteNoteConfirmation;

        function editExistingNote(dateKey) {
            const existingNote = notesData[dateKey];
            if (existingNote) {
                document.getElementById('notes').value = existingNote.notes;
                document.getElementById('urgency').value = existingNote.urgency;
                
                noteForm.classList.remove('hidden');
                noteListElement.innerHTML = ''; 
                
                document.getElementById('save-button').textContent = 'Update Notes';
            }
        };

        function deleteNoteConfirmation(dateKey) {
            const confirmation = document.getElementById('confirmation-dialog');
            document.getElementById('confirm-message').textContent = `Anda yakin ingin menghapus notes pada tanggal ${dateKey}?`;
            
            document.getElementById('confirm-yes').onclick = async () => {
                confirmation.classList.add('hidden');
                closeModal(); 
                await deleteNote(dateKey); 
            };

            document.getElementById('confirm-no').onclick = () => {
                confirmation.classList.add('hidden');
            };

            confirmation.classList.remove('hidden');
        };


        async function deleteNote(dateKey) {
            if (!db) {
                document.getElementById('status-message').textContent = "ERROR: Database tidak terhubung. Hapus GAGAL.";
                return;
            }
            
            try {
                const docRef = doc(db, getCollectionPath(), dateKey);
                
                await setDoc(docRef, { notes: null, urgency: null, isDeleted: true }, { merge: true });
                
                console.log("Notes marked as deleted for:", dateKey);
                document.getElementById('status-message').textContent = "Notes berhasil dihapus!";
                
            } catch (e) {
                console.error("Error deleting note:", e);
                document.getElementById('status-message').textContent = "Gagal menghapus notes.";
            }
        };

        // 7. Saving Notes
        noteForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!db) {
                 document.getElementById('status-message').textContent = "ERROR: Database tidak terhubung. Notes GAGAL disimpan.";
                 console.error("Attempted to save note but Firestore (db) is not initialized.");
                 return;
            }

            const notes = document.getElementById('notes').value.trim();
            const urgency = document.getElementById('urgency').value;

            if (!notes || !selectedDate || !currentUserId) {
                document.getElementById('status-message').textContent = "Mohon isi semua kolom dan pastikan user sudah terautentikasi.";
                return;
            }

            const docRef = doc(db, getCollectionPath(), selectedDate);
            const noteData = {
                notes: notes,
                urgency: urgency,
                updatedAt: new Date().toISOString(),
                userId: currentUserId,
            };
            
            if (!notesData[selectedDate] || !notesData[selectedDate].createdAt) {
                 noteData.createdAt = new Date().toISOString();
            }

            try {
                await setDoc(docRef, noteData, { merge: true }); 
                document.getElementById('status-message').textContent = "Notes berhasil disimpan!";
                console.log("Notes saved with ID:", selectedDate);
                
                displayNotesForSelectedDate(selectedDate);
                
                noteForm.reset();

            } catch (e) {
                console.error("Error adding document: ", e);
                document.getElementById('status-message').textContent = `Gagal menyimpan notes: ${e.message}`; 
            }
        });
        
        // 8. Event Listener
        document.getElementById('prev-month').onclick = () => changeMonth(-1);
        document.getElementById('next-month').onclick = () => changeMonth(1);
        document.getElementById('close-modal-btn').onclick = () => closeModal();

        // 9. Startup sequence
        renderCalendar(); 
        if (FIREBASE_CONFIG_JSON.apiKey) {
            initializeAuth(); 
        }
    </script>
</body>
</html>
