import os
import time
from subprocess import Popen
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler

WATCH_PATHS = ['./App', './Core', './Routes', './Includes']

class ChangeHandler(FileSystemEventHandler):
    def on_modified(self, event):
        if not event.is_directory and event.src_path.endswith('.php'):
            print(f"✨ Ficheiro alterado: {event.src_path}. A reiniciar o servidor...")
            # Simplesmente reinicia o serviço 'app', que irá reconstruir se necessário
            # e aplicar as alterações de código, já que o comando 'php horus serve'
            # é o comando principal do contentor.
            Popen("docker compose restart app", shell=True).wait()

if __name__ == "__main__":
    # Garante que os contentores estão a correr antes de iniciar o watcher
    print("🚀 Iniciando o ambiente Docker e o servidor Horus Nexus...")
    Popen("docker compose up -d --build", shell=True).wait()

    # Mostra os logs do servidor em tempo real
    logs_process = Popen("docker compose logs -f app", shell=True)

    event_handler = ChangeHandler()
    observer = Observer()
    for path in WATCH_PATHS:
        if os.path.exists(path):
            observer.schedule(event_handler, path, recursive=True)
            print(f"👀 A observar: {path}")

    observer.start()
    print("--- Horus Hot-Reload Ativado. Pressione Ctrl+C para parar. ---")
    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        observer.stop()
    finally:
        print("\n🛑 A parar os contentores Docker...")
        if logs_process:
            logs_process.terminate()
        Popen("docker compose down", shell=True).wait()
        observer.join()