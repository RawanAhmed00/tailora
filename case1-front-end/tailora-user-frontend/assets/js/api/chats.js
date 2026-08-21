/**
 * TAILORA USER — CHAT API SERVICE
 * Authenticated endpoints matching Chat API documentation:
 * - GET /chats
 * - GET /chats/{id}
 * - POST /chats/{id}/messages
 * - PUT /chats/{id}/read
 * - GET /chats/unread-count
 * - DELETE /chats/messages/{id}
 */
(function () {
  "use strict";

  const ChatApi = {
    // GET /chats
    async getChats() {
      return await window.TL.Api.get("/chats");
    },
    all() {
      return this.getChats();
    },

    // GET /chats/{id}
    async getChatMessages(id) {
      return await window.TL.Api.get(`/chats/${id}`);
    },
    messages(id) {
      return this.getChatMessages(id);
    },

    // POST /chats/{id}/messages
    async sendMessage(id, messageText, tripId = null) {
      const payload = {
        message: messageText,
        receiver_id: id,
        user_id: id,
        recipient_id: id
      };
      if (tripId) {
        payload.trip_id = tripId;
      }
      return await window.TL.Api.post(`/chats/${id}/messages`, payload);
    },

    // PUT /chats/{id}/read
    async markAsRead(id) {
      return await window.TL.Api.put(`/chats/${id}/read`);
    },
    markRead(id) {
      return this.markAsRead(id);
    },

    // GET /chats/unread-count
    async getUnreadCount() {
      return await window.TL.Api.get("/chats/unread-count");
    },
    unreadCount() {
      return this.getUnreadCount();
    },

    // DELETE /chats/messages/{id}
    async deleteMessage(messageId) {
      return await window.TL.Api.delete(`/chats/messages/${messageId}`);
    }
  };

  window.TL = window.TL || {};
  window.TL.ChatApi = ChatApi;
  window.TL.Chats = ChatApi;
})();
