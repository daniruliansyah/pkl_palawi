@extends('layouts.dashboard')

@section('title', 'Catatan Kalender Pribadi')

{{-- Tambahkan library Axios untuk komunikasi API --}}
@section('head')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
@endsection

@section('content')
<div class="p-4 mx-auto max-w-7xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Kalender Catatan Pribadi</h1>
    <p class="text-sm text-gray-500 mb-6">Catatan ini terikat pada NIP Anda ({{ Auth::user()->nip ?? 'NIP Tidak Ditemukan' }}) dan bersifat privat. Data diambil dari database.</p>

    <div x-data="calendarApp()" x-init="initCalendar()" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Kalender --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg border">
            <div class="flex justify-between items-center mb-6">
                <button @click="prevMonth()" class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <h2 x-text="`${monthNames[currentMonth]} ${currentYear}`" class="text-2xl font-semibold text-gray-700"></h2>
                <button @click="nextMonth()" class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>

            {{-- Grid Hari --}}
            <div class="grid grid-cols-7 gap-1 text-center text-sm font-medium text-gray-500 mb-2">
                <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
            </div>

            {{-- Grid Tanggal --}}
            <div class="grid grid-cols-7 gap-1">
                <template x-for="day in daysInMonth" :key="day.dateKey">
                    <div class="h-20 p-1 flex flex-col transition-all duration-150"
                        :class="{
                            'text-gray-400': !day.isCurrentMonth,
                            'bg-gray-50': day.isToday,
                            'border-2 border-blue-400 rounded-lg': day.dateKey === selectedDate,
                            'hover:bg-blue-50 cursor-pointer rounded-lg': day.isCurrentMonth
                        }"
                        @click="day.isCurrentMonth && selectDate(day.dateKey)">

                        <span class="text-xs font-semibold"
                            :class="{ 'text-blue-600': day.isToday && day.isCurrentMonth }">
                            <span x-text="day.date.getDate()"></span>
                        </span>

                        {{-- Indikator notes --}}
                        <div x-show="notesData[day.dateKey]" class="mt-1 w-full flex justify-center">
                            <div class="h-4 w-10 rounded-full"
                                :class="{
                                    'bg-red-500': notesData[day.dateKey]?.urgency === 'high',
                                    'bg-yellow-500': notesData[day.dateKey]?.urgency === 'medium',
                                    'bg-green-500': notesData[day.dateKey]?.urgency === 'low'
                                }">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Kolom Kanan: Detail Catatan --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-lg border">
            <h3 class="text-xl font-semibold mb-4 text-gray-700">
                Catatan Tanggal: <span x-text="formatSelectedDate()" class="text-blue-600"></span>
            </h3>

            {{-- Saat Loading --}}
            <div x-show="isLoading" class="text-center py-10">
                <svg class="animate-spin h-6 w-6 text-blue-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-500 mt-2">Memuat data...</p>
            </div>

            {{-- Saat Tidak Ada Catatan --}}
            <div x-show="!isLoading && !notesData[selectedDate] && selectedDate !== null" class="text-center py-10 text-gray-500">
                <p>Tidak ada catatan untuk tanggal ini.</p>
                <button @click="currentNote = { id: null, notes: '', urgency: 'medium', note_date: selectedDate }; hasNotes = true" class="mt-3 text-blue-600 hover:text-blue-800 font-medium">Buat Catatan Baru</button>
            </div>

            {{-- Form Catatan (Muncul jika ada catatan atau jika tombol 'Buat Catatan Baru' ditekan) --}}
            <div x-show="!isLoading && (notesData[selectedDate] || currentNote.id === null)">
                <form @submit.prevent="saveNote()">
                    <div class="mb-4">
                        <label for="note_text" class="block text-sm font-medium text-gray-700">Isi Catatan</label>
                        <textarea id="note_text" x-model="currentNote.notes" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tulis catatan Anda di sini..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="urgency_level" class="block text-sm font-medium text-gray-700">Tingkat Prioritas</label>
                        <select id="urgency_level" x-model="currentNote.urgency" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="low">Rendah (Hijau)</option>
                            <option value="medium">Sedang (Kuning)</option>
                            <option value="high">Tinggi (Merah)</option>
                        </select>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none">
                            <span x-text="currentNote.id ? 'Perbarui Catatan' : 'Simpan Catatan'">Simpan Catatan</span>
                        </button>

                        <button type="button" x-show="currentNote.id" @click="confirmDeleteNote()" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none">
                            Hapus
                        </button>
                    </div>
                </form>

                <div x-show="message"
                    :class="messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    class="p-3 mt-4 rounded-md text-sm"
                    x-text="message">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function calendarApp() {
        return {
            currentDate: new Date(),
            currentMonth: 0,
            currentYear: 0,
            monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
            daysInMonth: [],

            notesData: {},
            selectedDate: null,
            currentNote: {
                id: null,
                notes: '',
                urgency: 'medium',
                note_date: null
            },

            isLoading: false,
            message: '',
            messageType: 'success',

            initCalendar() {
                this.currentMonth = this.currentDate.getMonth();
                this.currentYear = this.currentDate.getFullYear();

                // Set default selectedDate ke tanggal hari ini (penting untuk form)
                const today = new Date();
                this.selectedDate = this.formatDate(today);

                this.renderCalendar();
                this.fetchNotes();
            },

            // --- UTILITY FUNCTIONS ---

            // Mengubah objek Date menjadi string YYYY-MM-DD
            formatDate(date) {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            },

            // Mengubah string tanggal dari API (misal: "2025-10-02T...") menjadi YYYY-MM-DD
            dateToString(dateString) {
                // Cukup ambil 10 karakter pertama (YYYY-MM-DD)
                return dateString.substring(0, 10);
            },

            // Mengubah YYYY-MM-DD menjadi format tampilan yang lebih mudah dibaca
            formatSelectedDate() {
                if (!this.selectedDate) return '';
                const parts = this.selectedDate.split('-');
                // Format: DD-MM-YYYY (disesuaikan dengan tampilan)
                return `${parts[2]}-${parts[1]}-${parts[0]}`;
            },

            // --- CALENDAR LOGIC ---

            renderCalendar() {
                this.daysInMonth = [];
                const firstDayOfMonth = new Date(this.currentYear, this.currentMonth, 1);
                const lastDayOfMonth = new Date(this.currentYear, this.currentMonth + 1, 0);
                const startDayOfWeek = firstDayOfMonth.getDay();
                const daysInPrevMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
                const todayKey = this.formatDate(new Date());

                // Hari dari bulan sebelumnya
                for (let i = startDayOfWeek; i > 0; i--) {
                    const date = new Date(this.currentYear, this.currentMonth - 1, daysInPrevMonth - i + 1);
                    this.daysInMonth.push({ date, dateKey: this.formatDate(date), isCurrentMonth: false, isToday: false });
                }

                // Hari di bulan ini
                for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
                    const date = new Date(this.currentYear, this.currentMonth, i);
                    const dateKey = this.formatDate(date);
                    this.daysInMonth.push({ date, dateKey, isCurrentMonth: true, isToday: dateKey === todayKey });
                }

                // Hari dari bulan berikutnya
                const totalCells = this.daysInMonth.length;
                const remainingCells = 42 - totalCells;
                for (let i = 1; i <= remainingCells; i++) {
                    const date = new Date(this.currentYear, this.currentMonth + 1, i);
                    this.daysInMonth.push({ date, dateKey: this.formatDate(date), isCurrentMonth: false, isToday: false });
                }
            },

            prevMonth() {
                this.currentMonth--;
                if (this.currentMonth < 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                }
                this.renderCalendar();
            },

            nextMonth() {
                this.currentMonth++;
                if (this.currentMonth > 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                }
                this.renderCalendar();
            },

            selectDate(dateKey) {
                this.selectedDate = dateKey;
                this.loadNoteForSelectedDate();
            },

            loadNoteForSelectedDate() {
                this.currentNote.note_date = this.selectedDate;
                this.message = '';

                const note = this.notesData[this.selectedDate];
                if (note) {
                    // Spread operator (...) untuk mengcopy properti note yang ada
                    this.currentNote = { ...note };
                } else {
                    // Reset form ke keadaan kosong jika tidak ada catatan
                    this.currentNote = { id: null, notes: '', urgency: 'medium', note_date: this.selectedDate };
                }
            },

            // --- API CALLS ---

            fetchNotes() {
                this.isLoading = true;
                this.notesData = {}; // Reset data

                // Tidak perlu mengirim NIP di query string karena sudah diambil di Controller dari Auth::user()
                axios.get(`/calendar/notes`)
                .then(response => {
                    if (response.data.status === 'success' && response.data.notes) {
                        response.data.notes.forEach(note => {
                            // KRITIKAL: Konversi format tanggal API ke format YYYY-MM-DD
                            const dateKey = this.dateToString(note.note_date);
                            this.notesData[dateKey] = note;
                        });
                    }
                    this.loadNoteForSelectedDate();
                })
                .catch(error => {
                    console.error('Error fetching notes:', error.response || error);
                    this.showStatus('Gagal memuat catatan. Cek koneksi API.', 'error');
                })
                .finally(() => {
                    this.isLoading = false;
                });
            },

            saveNote() {
                this.isLoading = true;
                this.message = '';
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const data = {
                    note_date: this.selectedDate,
                    notes: this.currentNote.notes,
                    urgency: this.currentNote.urgency
                };

                axios.post('{{ route('calendar.api.store') }}', data, {
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(response => {
                    if (response.data.status === 'success') {
                        // KRITIKAL: Gunakan tanggal yang diformat dari respons data
                        const dateKey = this.dateToString(response.data.data.note_date);

                        this.notesData[dateKey] = response.data.data;
                        this.currentNote = { ...response.data.data }; // Update currentNote
                        this.showStatus(response.data.message, 'success');
                    }
                })
                // ... (Fungsi catch dan finally lainnya tetap sama)
                .catch(error => {
                    let errorMessage = 'Gagal menyimpan catatan.';
                    if (error.response && error.response.data.errors) {
                        errorMessage = Object.values(error.response.data.errors).flat().join('. ');
                    }
                    this.showStatus(errorMessage, 'error');
                    console.error('Error saving note:', error.response || error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
            },

            // ... (Fungsi deleteNote() dan showStatus() lainnya tetap sama)

            confirmDeleteNote() {
                if (!confirm('Apakah Anda yakin ingin menghapus catatan ini?')) return;
                this.deleteNote();
            },

            deleteNote() {
                this.isLoading = true;
                this.message = '';
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                axios.delete(`{{ url('calendar/notes') }}/${this.currentNote.id}`, {
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(response => {
                    if (response.data.status === 'success') {
                        delete this.notesData[this.selectedDate];
                        this.loadNoteForSelectedDate();
                        this.showStatus(response.data.message, 'success');
                    }
                })
                .catch(error => {
                    this.showStatus('Gagal menghapus catatan.', 'error');
                    console.error('Error deleting note:', error.response || error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
            },

            showStatus(msg, type) {
                this.message = msg;
                this.messageType = type;
                setTimeout(() => this.message = '', 5000);
            }
        }
    }
</script>
@endsection
