<div
 class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6"
>
 {{-- Alert sukses (Pop-up) --}}
 @if(session('success'))
   <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
     {{ session('success') }}
   </div>
 @endif

 <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
   <div>
     <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
       Daftar Pengajuan Cuti
     </h3>
   </div>
 </div>

 <div class="w-full overflow-x-auto">
   <table class="min-w-full text-sm text-left">
     <thead>
       <tr class="border-y border-gray-100 dark:border-gray-800 bg-gray-50">
         <th class="py-3 px-4">No. Surat</th>
         <th class="py-3 px-4">Dibuat Oleh</th>
         <th class="py-3 px-4">Keterangan</th>
         <th class="py-3 px-4">Tanggal Upload</th>
         <th class="py-3 px-4">Persetujuan SSDM</th>
         <th class="py-3 px-4">Persetujuan GM</th>
         <th class="py-3 px-4">Persetujuan SDM</th>
         <th class="px-4 py-3">File Izin</th>
         <th class="py-3 px-4">Status</th>
         <th class="py-3 px-4">Aksi</th>
       </tr>
     </thead>

     <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
         @forelse($cutis as $item)
             <tr class="dark:hover:bg-gray-700">
               <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                    {{ $item->no_surat ?? '-' }}
               </td>
               <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                   {{ $item->user->nama_lengkap ?? '-' }}
               </td>
               <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                    {{ $item->keterangan }}
                </td>
               <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                   {{ $item->created_at->format('d-m-Y') }}
               </td>
               <td class="px-4 py-3">
                   <span @class([
                       'px-2 py-1 font-medium text-xs rounded-full',
                       'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => $item->status_sdm == 'Disetujui',
                       'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => $item->status_sdm == 'Ditolak',
                       'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' => $item->status_sdm != 'Disetujui' && $item->status_sdm != 'Ditolak',
                   ])>
                       {{ $item->status_sdm }}
                   </span>
               </td>
               <td class="px-4 py-3 font-medium
                   @if($item->status_gm === 'Disetujui' && $item->status_sdm === 'Disetujui')
                       text-green-700 dark:text-green-400
                   @elseif($item->status_gm === 'Ditolak' || $item->status_sdm === 'Ditolak')
                       text-red-700 dark:text-red-400
                   @else
                       text-gray-500 dark:text-gray-400
                   @endif">
                   @if($item->status_gm === 'Disetujui' && $item->status_sdm === 'Disetujui')
                       Selesai
                   @elseif($item->status_gm === 'Ditolak' || $item->status_sdm === 'Ditolak')
                       Ditolak
                   @else
                       Dalam Proses
                   @endif
               </td>
               <td class="px-4 py-3">
                   @php
                       $userJabatan = auth()->user()->jabatanTerbaru->jabatan->nama_jabatan ?? '';
                       $isGM = $userJabatan == 'General Manager';
                       $isSDM = $userJabatan == 'Senior Analis Keuangan, SDM & Umum';
                       $canApprove = ($isGM && $item->status_gm == 'menunggu') || ($isSDM && $item->status_sdm == 'menunggu');
                   @endphp
                   @if($item->status_gm === 'Disetujui' && $item->status_sdm === 'Disetujui')
                       <a href="{{ route('sppd.download', $item->id) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-blue-700">
                           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                               <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.146a.75.75 0 0 0-1.079-.675l-4.479 2.146-.013.006-.05.022a.598.598 0 0 1-.352.016l-4.478-2.146A.75.75 0 0 0 4.5 12.104V14.25m.75-2.146a.75.75 0 0 0-1.079-.675L4.5 12.104V14.25m.75-2.146a.75.75 0 0 0-1.079-.675L4.5 12.104V14.25m15 0v2.146a.75.75 0 0 1-1.079.675L9 16.104V14.25m10.5-2.146a.75.75 0 0 0-1.079-.675L9 16.104V14.25M6.75 14.25v2.146a.75.75 0 0 0 1.079.675l4.479-2.146.013-.006.05-.022a.598.598 0 0 1 .352-.016l4.478 2.146A.75.75 0 0 0 19.5 16.396V14.25" />
                           </svg>
                           Lihat Surat
                       </a>
                   @elseif($item->status_gm === 'Ditolak' || $item->status_sdm === 'Ditolak')
                       <span class="text-red-500 italic text-xs">Ditolak oleh {{ $item->status_gm === 'Ditolak' ? 'GM' : 'SDM' }}</span>
                   @elseif($canApprove)
                       <div x-data="{ openModal: false }">
                           <form action="{{ route('sppd.updateStatus', $item->id) }}" method="POST" class="inline-block">
                               @csrf
                               @method('PATCH')
                               <input type="hidden" name="role" value="{{ $userJabatan }}">
                               <button name="status" value="Disetujui" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded">✅ Terima</button>
                           </form>

                           <button @click="openModal = true" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded ml-1">❌ Tolak</button>

                           <div x-show="openModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black/50">
                               <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96 relative">
                                   <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Alasan Penolakan</h3>
                                   <form action="{{ route('sppd.updateStatus', $item->id) }}" method="POST">
                                       @csrf
                                       @method('PATCH')
                                       <input type="hidden" name="role" value="{{ $userJabatan }}">
                                       <input type="hidden" name="status" value="Ditolak">
                                       <textarea name="reason" rows="4" class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white" placeholder="Masukkan alasan penolakan..." required></textarea>
                                       <div class="flex justify-end gap-2 mt-4">
                                           <button type="button" @click="openModal = false" class="px-3 py-1 rounded border">Batal</button>
                                           <button type="submit" class="px-3 py-1 rounded bg-red-500 text-white">Kirim</button>
                                       </div>
                                   </form>
                               </div>
                           </div>
                       </div>
                   @else
                       <span class="text-gray-400 italic text-xs">Sudah diproses</span>
                   @endif
               </td>
             </tr>
         @empty
             <tr>
                 <td colspan="7" class="py-4 text-center text-gray-500 dark:text-gray-400">
                     Belum ada pengajuan SPPD.
                 </td>
             </tr>
         @endforelse
     </tbody>
   </table>
 </div>
</div>
