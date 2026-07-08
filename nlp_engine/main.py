from fastapi import FastAPI
from pydantic import BaseModel
from transformers import pipeline
import os
import re

app = FastAPI(title="Sistem Klasifikasi Tiket Bantuan NLP PTPN IV")

MODEL_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), "model_offline")

# Load model dari folder lokal
print("Memuat Model AI dari folder lokal...")
classifier = pipeline("zero-shot-classification", model=MODEL_DIR)
print("Model AI siap menerima permintaan!")

# Pemetaan label natural ke kategori database (10 Kategori Resmi)
LABEL_MAPPING = {
    "jaringan komputer dan internet": "Network",
    "perangkat keras komputer": "Hardware",
    "perangkat lunak aplikasi": "Software",
    "basis data dan penyimpanan tabel": "Database",
    "server pusat dan infrastruktur": "Server",
    "penyimpanan file dan kapasitas disk": "Storage",
    "cctv dan sistem pengawasan keamanan": "CCTV & Security Systems",
    "portal web dan situs intranet perusahaan": "Web & Intranet Portal",
    "keamanan sistem komputer dan infeksi virus": "Keamanan & Virus",
    "perangkat tambahan seperti printer dan scanner": "Peripheral"
}
REVERSE_MAPPING = {v: k for k, v in LABEL_MAPPING.items()}

# Kamus normalisasi singkatan
abbreviation_dict = {
    r"\bpw\b": "password",
    r"\bkonek\b": "koneksi",
    r"\bgabisa\b": "tidak bisa",
    r"\bgagal\b": "error",
    r"\bbapuk\b": "lambat"
}

def preprocess_text(text: str) -> str:
    text = text.lower()
    text = re.sub(r"http\S+|www\S+|https\S+", '', text, flags=re.MULTILINE)
    text = re.sub(r"[^\w\s\.,-]", ' ', text)
    
    for pattern, replacement in abbreviation_dict.items():
        text = re.sub(pattern, replacement, text)
        
    text = re.sub(r'\s+', ' ', text).strip()
    return text

class TicketInput(BaseModel):
    description: str
    categories: list[str] = None
    threshold: float = 0.20

@app.get("/")
def read_root():
    return {"message": "API NLP Helpdesk PTPN IV Berjalan Normal"}

@app.post("/predict")
def predict_category(ticket: TicketInput):
    db_categories = ticket.categories if ticket.categories else list(LABEL_MAPPING.values())
    
    # Konversi ke label natural untuk akurasi yang lebih baik
    candidate_labels = [REVERSE_MAPPING.get(cat, cat.lower()) for cat in db_categories]

    cleaned_text = preprocess_text(ticket.description)
    
    result = classifier(
        cleaned_text, 
        candidate_labels,
        hypothesis_template="Laporan ini berkaitan dengan masalah {}."
    )
    
    top_natural_label = result['labels'][0]
    best_score = result['scores'][0]
    
    # Petakan kembali ke nama kategori database
    idx = candidate_labels.index(top_natural_label)
    best_label = db_categories[idx]
    
    # Confidence threshold (dinamis dari parameter request)
    CONFIDENCE_THRESHOLD = ticket.threshold
    if best_score < CONFIDENCE_THRESHOLD:
        best_label = "Lain-lain"
    
    return {
        "original_text": ticket.description,
        "cleaned_text": cleaned_text,
        "predicted_category": best_label,
        "confidence_score": round(best_score * 100, 2),
        "is_confident": best_score >= CONFIDENCE_THRESHOLD,
        "natural_prediction": top_natural_label
    }
