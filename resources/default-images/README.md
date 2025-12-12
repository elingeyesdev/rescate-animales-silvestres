# 📸 Imágenes por Defecto

Esta carpeta contiene imágenes por defecto que se copiarán automáticamente al storage cuando se inicie Docker.

## 📁 Estructura

```
resources/default-images/
├── personas/
│   └── persona.png    # Imagen por defecto para perfiles de usuario
└── README.md
```

## 🚀 Cómo Funciona

1. **Coloca tus imágenes aquí**: Agrega las imágenes que quieras que estén disponibles por defecto en las carpetas correspondientes.

2. **Se copian automáticamente**: El script `entrypoint.sh` copia estas imágenes a `storage/app/public/` cuando Docker se inicia, pero solo si no existen ya.

3. **Incluidas en Git**: Estas imágenes están en el repositorio, por lo que estarán disponibles en cualquier entorno.

## 📝 Agregar una Nueva Imagen por Defecto

1. Coloca tu imagen en la carpeta correspondiente:
   ```bash
   # Ejemplo: agregar una imagen de animal por defecto
   cp mi-imagen.jpg resources/default-images/animales/default.jpg
   ```

2. Actualiza el `entrypoint.sh` si necesitas copiar a una nueva carpeta:
   ```bash
   mkdir -p storage/app/public/animales || true
   if [ -d "resources/default-images/animales" ]; then
       for img in resources/default-images/animales/*; do
           if [ -f "$img" ]; then
               filename=$(basename "$img")
               if [ ! -f "storage/app/public/animales/$filename" ]; then
                   cp "$img" "storage/app/public/animales/$filename"
               fi
           fi
       done
   fi
   ```

3. Usa la imagen en tu código:
   ```php
   $imagenUrl = asset('storage/animales/default.jpg');
   ```

## ⚠️ Nota Importante

- Las imágenes aquí se copian solo si **no existen** en el storage
- Si ya existe una imagen con el mismo nombre, no se sobrescribe
- Estas imágenes están en Git, así que úsalas para imágenes pequeñas y públicas

