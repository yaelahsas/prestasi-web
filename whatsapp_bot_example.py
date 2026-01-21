#!/usr/bin/env python3
"""
Example WhatsApp Bot Integration with Prestasi Jurnal API
This is a simplified example showing how to integrate the API with a WhatsApp bot
"""

import requests
import json
import base64
from datetime import datetime

class PrestasiAPI:
    def __init__(self, base_url, api_key):
        self.base_url = base_url.rstrip('/') + '/api/'
        self.headers = {
            'Content-Type': 'application/json',
            'X-API-Key': api_key
        }
    
    def get_guru_list(self):
        """Get list of guru"""
        response = requests.get(
            self.base_url + 'guru/list',
            headers=self.headers
        )
        return response.json()
    
    def get_kelas_list(self):
        """Get list of kelas"""
        response = requests.get(
            self.base_url + 'kelas/list',
            headers=self.headers
        )
        return response.json()
    
    def get_mapel_list(self):
        """Get list of mapel"""
        response = requests.get(
            self.base_url + 'mapel/list',
            headers=self.headers
        )
        return response.json()
    
    def create_jurnal(self, data):
        """Create a new jurnal"""
        response = requests.post(
            self.base_url + 'jurnal/create',
            headers=self.headers,
            json=data
        )
        return response.json()
    
    def get_jurnal_list(self, page=1, limit=10):
        """Get list of jurnal with pagination"""
        params = {'page': page, 'limit': limit}
        response = requests.get(
            self.base_url + 'jurnal/list',
            headers=self.headers,
            params=params
        )
        return response.json()
    
    def search_jurnal(self, keyword):
        """Search jurnal by keyword"""
        params = {'keyword': keyword}
        response = requests.get(
            self.base_url + 'jurnal/search',
            headers=self.headers,
            params=params
        )
        return response.json()

class WhatsAppBot:
    def __init__(self, api_url, api_key):
        self.api = PrestasiAPI(api_url, api_key)
        self.guru_cache = {}
        self.kelas_cache = {}
        self.mapel_cache = {}
        self._load_reference_data()
    
    def _load_reference_data(self):
        """Load and cache reference data"""
        try:
            # Load guru data
            guru_response = self.api.get_guru_list()
            if guru_response['status'] == 'success':
                for guru in guru_response['data']:
                    self.guru_cache[guru['nama_guru'].lower()] = guru['id_guru']
            
            # Load kelas data
            kelas_response = self.api.get_kelas_list()
            if kelas_response['status'] == 'success':
                for kelas in kelas_response['data']:
                    self.kelas_cache[kelas['nama_kelas'].lower()] = kelas['id_kelas']
            
            # Load mapel data
            mapel_response = self.api.get_mapel_list()
            if mapel_response['status'] == 'success':
                for mapel in mapel_response['data']:
                    self.mapel_cache[mapel['nama_mapel'].lower()] = mapel['id_mapel']
                    
        except Exception as e:
            print(f"Error loading reference data: {e}")
    
    def parse_jurnal_message(self, message):
        """Parse WhatsApp message to extract jurnal data"""
        lines = message.strip().split('\n')
        data = {}
        
        for line in lines:
            line = line.strip()
            if ':' in line:
                key, value = line.split(':', 1)
                key = key.strip().lower()
                value = value.strip()
                
                if key == 'tanggal':
                    data['tanggal'] = value
                elif key == 'guru':
                    guru_name = value.lower()
                    if guru_name in self.guru_cache:
                        data['id_guru'] = self.guru_cache[guru_name]
                elif key == 'kelas':
                    kelas_name = value.lower()
                    if kelas_name in self.kelas_cache:
                        data['id_kelas'] = self.kelas_cache[kelas_name]
                elif key == 'mapel':
                    mapel_name = value.lower()
                    if mapel_name in self.mapel_cache:
                        data['id_mapel'] = self.mapel_cache[mapel_name]
                elif key == 'materi':
                    data['materi'] = value
                elif key == 'jumlah siswa':
                    try:
                        data['jumlah_siswa'] = int(value)
                    except ValueError:
                        pass
                elif key == 'keterangan':
                    data['keterangan'] = value
        
        return data
    
    def create_jurnal_from_message(self, message, image_path=None):
        """Create jurnal from WhatsApp message"""
        # Parse the message
        data = self.parse_jurnal_message(message)
        
        # Validate required fields
        required_fields = ['tanggal', 'id_guru', 'id_kelas', 'id_mapel', 'materi', 'jumlah_siswa']
        missing_fields = [field for field in required_fields if field not in data]
        
        if missing_fields:
            return {
                'status': 'error',
                'message': f'Missing required fields: {", ".join(missing_fields)}'
            }
        
        # Handle image if provided
        if image_path:
            try:
                with open(image_path, 'rb') as image_file:
                    encoded_image = base64.b64encode(image_file.read()).decode('utf-8')
                    # Determine image type
                    if image_path.lower().endswith(('.jpg', '.jpeg')):
                        data['foto_bukti'] = f'data:image/jpeg;base64,{encoded_image}'
                    elif image_path.lower().endswith('.png'):
                        data['foto_bukti'] = f'data:image/png;base64,{encoded_image}'
            except Exception as e:
                print(f"Error processing image: {e}")
        
        # Set default created_by if not provided
        if 'created_by' not in data:
            data['created_by'] = 1
        
        # Create jurnal via API
        return self.api.create_jurnal(data)
    
    def format_jurnal_list(self, jurnal_list):
        """Format jurnal list for WhatsApp message"""
        if not jurnal_list:
            return "Tidak ada jurnal ditemukan."
        
        message = "📚 *Daftar Jurnal*\n\n"
        
        for jurnal in jurnal_list[:5]:  # Limit to 5 items for WhatsApp
            message += f"📅 {jurnal['tanggal']}\n"
            message += f"👨‍🏫 {jurnal['nama_guru']}\n"
            message += f"🏫 {jurnal['nama_kelas']}\n"
            message += f"📖 {jurnal['nama_mapel']}\n"
            message += f"📝 {jurnal['materi'][:50]}{'...' if len(jurnal['materi']) > 50 else ''}\n"
            message += f"👥 {jurnal['jumlah_siswa']} siswa\n\n"
        
        return message
    
    def handle_message(self, message, image_path=None):
        """Handle incoming WhatsApp message"""
        message_lower = message.lower()
        
        # Check if it's a jurnal creation message
        if 'jurnal' in message_lower and ':' in message:
            result = self.create_jurnal_from_message(message, image_path)
            
            if result['status'] == 'success':
                return f"✅ Jurnal berhasil dibuat!\nID: {result['data']['id_jurnal']}"
            else:
                return f"❌ Gagal membuat jurnal: {result['message']}"
        
        # Check if it's a request to view jurnal
        elif 'lihat jurnal' in message_lower or 'tampilkan jurnal' in message_lower:
            result = self.api.get_jurnal_list(limit=5)
            
            if result['status'] == 'success':
                return self.format_jurnal_list(result['data']['jurnal'])
            else:
                return f"❌ Gagal mengambil data jurnal: {result['message']}"
        
        # Check if it's a search request
        elif 'cari jurnal' in message_lower:
            keyword = message_lower.replace('cari jurnal', '').strip()
            if keyword:
                result = self.api.search_jurnal(keyword)
                
                if result['status'] == 'success':
                    return self.format_jurnal_list(result['data'])
                else:
                    return f"❌ Gagal mencari jurnal: {result['message']}"
            else:
                return "Silakan masukkan kata kunci pencarian.\nContoh: cari jurnal matematika"
        
        # Help message
        else:
            return """🤖 *Bot Jurnal Prestasi*

Perintah yang tersedia:
1. Buat jurnal baru:
```
Jurnal
Tanggal: 2024-01-15
Guru: Ahmad Fauzi
Kelas: XII IPA 1
Mapel: Matematika
Materi: Pembahasan Soal
Jumlah Siswa: 25
Keterangan: Siswa antusias
```

2. Lihat jurnal:
`lihat jurnal`

3. Cari jurnal:
`cari jurnal matematika`

4. Bantuan:
`help`"""

# Example usage
if __name__ == "__main__":
    # Configuration
    API_URL = "http://localhost/prestasi"
    API_KEY = "whatsapp_bot_key_2024"
    
    # Initialize bot
    bot = WhatsAppBot(API_URL, API_KEY)
    
    # Example 1: Create jurnal from message
    jurnal_message = """Jurnal
Tanggal: 2024-01-15
Guru: Ahmad Fauzi
Kelas: XII IPA 1
Mapel: Matematika
Materi: Pembahasan Soal Matematika Kelas 12
Jumlah Siswa: 25
Keterangan: Siswa antusias mengikuti pembelajaran"""
    
    print("Example 1: Creating jurnal")
    response = bot.handle_message(jurnal_message)
    print(response)
    print("\n" + "="*50 + "\n")
    
    # Example 2: View jurnal list
    print("Example 2: Viewing jurnal list")
    response = bot.handle_message("lihat jurnal")
    print(response)
    print("\n" + "="*50 + "\n")
    
    # Example 3: Search jurnal
    print("Example 3: Searching jurnal")
    response = bot.handle_message("cari jurnal matematika")
    print(response)
    print("\n" + "="*50 + "\n")
    
    # Example 4: Help message
    print("Example 4: Help message")
    response = bot.handle_message("help")
    print(response)