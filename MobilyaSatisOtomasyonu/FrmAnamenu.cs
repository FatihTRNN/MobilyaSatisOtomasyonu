using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace MobilyaSatisOtomasyonu
{
    public partial class FrmAnamenu : Form
    {
        public FrmAnamenu()
        {
            InitializeComponent();
        }

        private void button1_Click(object sender, EventArgs e)
        {
            FrmUrunIslemleri frm = new FrmUrunIslemleri();
            frm.Show();

        }

        private void button2_Click(object sender, EventArgs e)
        {
            FrmMusteriIslemleri frm = new FrmMusteriIslemleri();
            frm.Show(); 
        }

        private void button3_Click(object sender, EventArgs e)
        {
            FrmSatisIslemleri frm = new FrmSatisIslemleri();
            frm.Show(); 
        }
    }
}
