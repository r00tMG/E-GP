from reportlab.lib.pagesizes import A4
from reportlab.pdfgen import canvas
from datetime import datetime
import os

def generate_invoice_pdf(reservation, payment, output_dir="invoices"):
    os.makedirs(output_dir, exist_ok=True)
    filename = f"{output_dir}/invoice_{reservation.id}.pdf"
    c = canvas.Canvas(filename, pagesize=A4)
    width, height = A4

    # Titre
    c.setFont("Helvetica-Bold", 20)
    c.drawString(50, height - 50, "Facture de Réservation")

    # Infos réservation
    c.setFont("Helvetica", 12)
    c.drawString(50, height - 100, f"Réservation ID: {reservation.id}")
    c.drawString(50, height - 120, f"Date: {datetime.utcnow().strftime('%Y-%m-%d %H:%M:%S')}")
    c.drawString(50, height - 140, f"Client: {reservation.user.first_name} {reservation.user.last_name}")
    c.drawString(50, height - 160, f"GP: {reservation.annonce.gp.first_name} {reservation.annonce.gp.last_name}")

    # Lignes items
    y = height - 200
    c.drawString(50, y, "Articles:")
    y -= 20
    for item in reservation.items:
        c.drawString(60, y, f"{item.item_name} - {item.weight} kg x {float(item.price_per_kg):.2f}€")
        y -= 20
    for s_item in reservation.special_items:
        c.drawString(60, y, f"{s_item.item_name} - {s_item.quantity} x {float(s_item.price_per_piece):.2f}€")
        y -= 20

    # Totaux
    y -= 10
    c.drawString(50, y, f"Total: {float(reservation.total_price):.2f}€")
    y -= 20
    c.drawString(50, y, f"Commission plateforme: {float(payment.commission):.2f}€")

    c.save()
    return filename
