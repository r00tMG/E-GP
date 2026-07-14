#FROM python:3.11-slim
#
#WORKDIR /app
#
#COPY requirements.txt /app/
#
#RUN pip install --no-cache-dir -r requirements.txt
#
#COPY . /app
#
#ENV HF_HOME=/app/hf_home
#
#RUN mkdir -p /app/hf_home && chmod -R 777 /app/hf_home
#
#CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "7860"]
FROM python:3.11-slim

WORKDIR /app

COPY requirements.txt /app/

RUN pip install --no-cache-dir -r requirements.txt

COPY . /app

ENV HF_HOME=/app/hf_home

COPY entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]