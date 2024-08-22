"use strict";

// Class definition
var KTLandingPage = function () {
    
    var initOrgChart = function () {
        var nodes = [
            { id: 1, name: "Hadi Adriansyah", title: "RUPS", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/media/hadi.jpg' },
            
            // Dewan Komisaris
            { id: 2, pid: 1, name: "Budi Santoso", title: "Dewan Komisaris", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
    
            // Direktur Utama
            { id: 3, pid: 1, name: "Andi Wijaya", title: "Direktur Utama", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 4, pid: 3, name: "Citra Dewi", title: "Divisi Pengawasan", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 5, pid: 3, name: "Dewi Lestari", title: "Direktur Kepatuhan", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 6, pid: 3, name: "Eko Prasetyo", title: "Direktur Keuangan dan Teknologi Informasi", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 7, pid: 3, name: "Fajar Nugroho", title: "Direktur Pemasaran", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 8, pid: 3, name: "Gita Permata", title: "Direktur Bisnis & Syariah", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
    
            // Direktur Kepatuhan
            { id: 9, pid: 5, name: "Hadi Susanto", title: "Divisi Kepatuhan", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 10, pid: 5, name: "Indra Kurniawan", title: "Divisi Manajemen Risiko", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 11, pid: 5, name: "Joko Widodo", title: "UKK APU & PTT", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
    
            // Direktur Keuangan dan Teknologi Informasi
            { id: 12, pid: 6, name: "Kiki Amalia", title: "Divisi Akuntansi dan Perencanaan", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 13, pid: 6, name: "Lina Marlina", title: "Divisi Tresuri", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 14, pid: 6, name: "Maya Sari", title: "Divisi Teknologi Informasi", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 15, pid: 6, name: "Nina Agustina", title: "Divisi Umum", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
    
            //Direktur Utama
            { id: 16, pid: 3, name: "Oki Setiawan", title: "Sekretariat Perusahaan", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 17, pid: 3, name: "Putu Widi", title: "Divisi Sumber Daya Manusia", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 18, pid: 3, name: "Qori Rahma", title: "Divisi Startegi & Transformasi", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 99, pid: 18, name: "Hadi Adriansyah", title: "TKD", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/media/hadi.jpg' },
    
            // Direktur Pemasaran
            { id: 19, pid: 7, name: "Rina Sari", title: "Divisi Dana & Jasa", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 20, pid: 7, name: "Siti Aminah", title: "Divisi Credit Review", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 21, pid: 7, name: "Tina Kartika", title: "Divisi Operasional", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
    
            // Direktur
            { id: 22, pid: 3, name: "Umar Bakri", title: "Kantor Cabang", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 23, pid: 22, name: "Vina Melati", title: "KC Pembantu", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
    
            // Direktur Bisnis & Syariah
            { id: 24, pid: 8, name: "Wawan Setiawan", title: "Divisi Kredit", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 25, pid: 8, name: "Xena Putri", title: "Divisi Ritel", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 26, pid: 8, name: "Yudi Pratama", title: "Divisi Penyelamatan Kredit", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 27, pid: 8, name: "Zaki Ramadhan", title: "Unit Usaha Syariah", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
    
            // Unit Usaha Syariah
            { id: 28, pid: 27, name: "Agus Salim", title: "Kantor Cabang Syariah", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
            { id: 29, pid: 28, name: "Bambang Hartono", title: "KC Pembantu Syariah", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
    
            // Dewan Pengawas Syariah
            { id: 30, pid: 1, name: "Cahyo Budi", title: "Dewan Pengawas Syariah", KPI: Math.floor(Math.random() * 3) + 3, img: siteUrl + 'assets/vendors/metronic-admin/dist/assets/media/avatars/150-' + Math.floor(Math.random() * 26 + 1) + '.jpg' },
          ];
    
          for (var i = 0; i < nodes.length; i++) {
            var node = nodes[i];
            switch (node.title) {
                case "RUPS":
                case "Direktur Utama":
                case "Dewan Komisaris":
                case "Dewan Pengawas Syariah":
                case "Unit Usaha Syariah":
                    node.tags = ["RUPS"];
                    break;
                case "Direktur Kepatuhan":
                case "Direktur Keuangan dan Teknologi Informasi":
                case "Direktur Pemasaran":
                case "Direktur Bisnis & Syariah":
                case "Kantor Cabang":
                case "Kantor Cabang Syariah":
                    node.tags = ["Direktur"];
                    break;
            }
        }
    
          var chart = new OrgChart(document.getElementById("tree"), {    
            mouseScrool: OrgChart.action.scroll,
            scaleInitial: 0.6,
            mode: 'dark',
            layout: OrgChart.mixed,
            nodeBinding: {
                field_0: "title",
                  field_1: "name",
                  img_0: "img"
              }
          });
    
        chart.load(nodes);
    }
    // Public methods
    return {
        init: function () {
            initOrgChart();
        }   
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTLandingPage.init();
});
