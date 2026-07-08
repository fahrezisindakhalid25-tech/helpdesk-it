import os
from transformers import AutoModelForSequenceClassification, AutoTokenizer

print("Mendownload dan menyimpan model mDeBERTa-v3-base-mnli-xnli...")

model_name = "MoritzLaurer/mDeBERTa-v3-base-mnli-xnli"
save_directory = "./model_offline"

os.makedirs(save_directory, exist_ok=True)

tokenizer = AutoTokenizer.from_pretrained(model_name)
model = AutoModelForSequenceClassification.from_pretrained(model_name)

tokenizer.save_pretrained(save_directory)
model.save_pretrained(save_directory)

print(f"Selesai! Model dan Tokenizer berhasil disimpan di folder: {save_directory}")
