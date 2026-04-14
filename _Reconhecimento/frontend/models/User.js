const mongoose = require('mongoose');

const usuarioSchema = new mongoose.Schema({
  nome: { type: String, required: true, trim: true },
  matricula: { type: String, required: true, unique: true, trim: true },
  senha: { type: String },
  faceDescriptor: { type: [Number], required: true },
  imagem: { type: String, required: true },
  dataCadastro: { type: Date, default: Date.now }
});

module.exports = mongoose.model('Usuario', usuarioSchema);
  